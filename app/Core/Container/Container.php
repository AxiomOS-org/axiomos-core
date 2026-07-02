<?php

declare(strict_types=1);

namespace App\Core\Container;

use App\Core\Container\Contracts\BootableProviderInterface;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Container\Contracts\DeferredProviderInterface;
use App\Core\Container\Contracts\ServiceProviderInterface;
use App\Core\Container\Events\ContainerFailed;
use App\Core\Container\Events\ContainerResolved;
use App\Core\Container\Events\ContainerResolving;
use App\Core\Container\Exceptions\CircularDependencyException;
use App\Core\Container\Exceptions\ContainerException;
use App\Core\Container\Exceptions\NotFoundException;
use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Throwable;

/**
 * Enterprise dependency-injection container for AxiomOS.
 *
 * PSR-11 compatible with auto-wiring, scoped lifetimes, tagged services,
 * deferred providers, lazy resolution, circular-dependency detection and
 * observability hooks for Prometheus/OpenTelemetry integration.
 */
final class Container implements ContainerInterface
{
    /** @var array<string, Binding> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    /** @var array<string, object> */
    private array $scopedInstances = [];

    /** @var array<string, string> */
    private array $aliases = [];

    /** @var array<string, list<string>> */
    private array $tags = [];

    /** @var list<string> */
    private array $buildStack = [];

    /** @var list<ServiceProviderInterface> */
    private array $providers = [];

    /** @var array<string, ServiceProviderInterface> */
    private array $deferredProviders = [];

    /** @var array<string, bool> */
    private array $loadedProviders = [];

    private bool $providersBooted = false;

    public function __construct(private readonly ?Dispatcher $events = null)
    {
        $this->instance(self::class, $this);
        $this->instance(ContainerInterface::class, $this);
    }

    public function bind(string $abstract, mixed $concrete = null, Scope $scope = Scope::Transient): void
    {
        $this->dropStaleResolution($abstract);
        $this->bindings[$abstract] = new Binding(
            abstract: $abstract,
            concrete: $concrete ?? $abstract,
            scope: $scope,
        );
    }

    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $this->bind($abstract, $concrete, Scope::Singleton);
    }

    public function scoped(string $abstract, mixed $concrete = null): void
    {
        $this->bind($abstract, $concrete, Scope::Scoped);
    }

    public function instance(string $abstract, object $instance): void
    {
        $this->dropStaleResolution($abstract);
        $this->instances[$abstract] = $instance;
        $this->bindings[$abstract] = new Binding(
            abstract: $abstract,
            concrete: $instance,
            scope: Scope::Singleton,
        );
    }

    public function alias(string $alias, string $abstract): void
    {
        $this->aliases[$alias] = $abstract;
    }

    public function tag(string $abstract, array $tags): void
    {
        foreach ($tags as $tag) {
            $this->tags[$tag] ??= [];
            if (! in_array($abstract, $this->tags[$tag], true)) {
                $this->tags[$tag][] = $abstract;
            }
        }

        if (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];
            $this->bindings[$abstract] = new Binding(
                abstract: $binding->abstract,
                concrete: $binding->concrete,
                scope: $binding->scope,
                lazy: $binding->lazy,
                tags: array_values(array_unique([...$binding->tags, ...$tags])),
            );
        }
    }

    public function tagged(string $tag): array
    {
        $services = [];

        foreach ($this->tags[$tag] ?? [] as $abstract) {
            $services[] = $this->make($abstract);
        }

        return $services;
    }

    public function lazy(string $abstract, mixed $concrete = null): void
    {
        $target = $concrete ?? $abstract;

        if (! is_string($target)) {
            throw new ContainerException('Lazy bindings require a class-string concrete.');
        }

        $this->dropStaleResolution($abstract);
        $this->bindings[$abstract] = new Binding(
            abstract: $abstract,
            concrete: fn (Container $container): object => $container->buildClass($target, []),
            scope: Scope::Singleton,
            lazy: true,
        );
    }

    public function get(string $id): mixed
    {
        return $this->make($id);
    }

    public function has(string $id): bool
    {
        $abstract = $this->getAlias($id);

        return isset($this->instances[$abstract])
            || isset($this->bindings[$abstract])
            || (! interface_exists($abstract) && class_exists($abstract));
    }

    public function make(string $abstract, array $parameters = []): mixed
    {
        $abstract = $this->getAlias($abstract);

        try {
            $this->loadDeferredProviderFor($abstract);
            $this->events?->dispatch(new ContainerResolving($abstract, $parameters));

            $object = $this->resolve($abstract, $parameters);

            if (is_object($object)) {
                $this->events?->dispatch(new ContainerResolved($abstract, $object));
            }

            return $object;
        } catch (Throwable $exception) {
            $this->events?->dispatch(new ContainerFailed($abstract, $exception));

            throw $exception;
        }
    }

    public function call(callable|array|string $callback, array $parameters = []): mixed
    {
        if (is_string($callback) && str_contains($callback, '::')) {
            $callback = explode('::', $callback, 2);
        }

        if (is_array($callback)) {
            [$class, $method] = $callback;

            if (is_object($class)) {
                $reflection = new ReflectionMethod($class, $method);
                $dependencies = $this->resolveDependencies($reflection, $parameters);

                return $reflection->invokeArgs($class, $dependencies);
            }

            $reflection = new ReflectionMethod($class, $method);

            if (! $reflection->isStatic()) {
                $instance = $this->make($class);
                $dependencies = $this->resolveDependencies($reflection, $parameters);

                return $reflection->invokeArgs($instance, $dependencies);
            }

            $dependencies = $this->resolveDependencies($reflection, $parameters);

            return $reflection->invokeArgs(null, $dependencies);
        }

        $reflection = new ReflectionFunction(Closure::fromCallable($callback));
        $dependencies = $this->resolveDependencies($reflection, $parameters);

        return $reflection->invokeArgs($dependencies);
    }

    public function registerProvider(ServiceProviderInterface $provider): void
    {
        $this->providers[] = $provider;

        if ($provider instanceof DeferredProviderInterface) {
            foreach ($provider->provides() as $service) {
                $this->deferredProviders[$service] = $provider;
            }

            return;
        }

        $provider->register($this);
        $this->markProviderLoaded($provider);
    }

    public function bootProviders(): void
    {
        if ($this->providersBooted) {
            return;
        }

        foreach ($this->providers as $provider) {
            if ($provider instanceof BootableProviderInterface) {
                $provider->boot($this);
            }
        }

        $this->providersBooted = true;
    }

    public function flushScoped(): void
    {
        $this->scopedInstances = [];
    }

    public function cache(?string $path = null): void
    {
        $path ??= $this->cachePath();

        if ($path === null) {
            throw new ContainerException('Cache path is not configured.');
        }

        $payload = [
            'bindings' => array_map(static fn (Binding $binding): array => [
                'abstract' => $binding->abstract,
                'concrete' => is_string($binding->concrete) ? $binding->concrete : null,
                'scope' => $binding->scope->value,
                'lazy' => $binding->lazy,
                'tags' => $binding->tags,
            ], $this->bindings),
            'aliases' => $this->aliases,
            'tags' => $this->tags,
        ];

        $exported = var_export($payload, true);
        file_put_contents($path, "<?php\n\ndeclare(strict_types=1);\n\nreturn {$exported};\n");
    }

    public function loadCache(string $path): bool
    {
        if (! is_file($path)) {
            return false;
        }

        /** @var array{bindings: list<array<string, mixed>>, aliases: array<string, string>, tags: array<string, list<string>>} $payload */
        $payload = require $path;

        foreach ($payload['bindings'] as $item) {
            if (! is_string($item['concrete'] ?? null)) {
                continue;
            }

            $this->bindings[$item['abstract']] = new Binding(
                abstract: $item['abstract'],
                concrete: $item['concrete'],
                scope: Scope::from($item['scope']),
                lazy: (bool) $item['lazy'],
                tags: $item['tags'],
            );
        }

        $this->aliases = $payload['aliases'];
        $this->tags = $payload['tags'];

        return true;
    }

    public function clearCache(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function resolve(string $abstract, array $parameters): mixed
    {
        if (isset($this->instances[$abstract]) && $parameters === []) {
            return $this->instances[$abstract];
        }

        $binding = $this->bindings[$abstract] ?? null;

        if ($binding?->isScoped() && isset($this->scopedInstances[$abstract]) && $parameters === []) {
            return $this->scopedInstances[$abstract];
        }

        if ($binding === null && ! class_exists($abstract)) {
            throw NotFoundException::forAbstract($abstract);
        }

        $concrete = $binding?->concrete ?? $abstract;
        $resolved = $this->build($concrete, $parameters);

        if (is_object($resolved)) {
            $this->cacheResolvedInstance($abstract, $binding, $resolved);
        }

        return $resolved;
    }

    private function build(mixed $concrete, array $parameters): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, ...array_values($parameters));
        }

        if (is_object($concrete)) {
            return $concrete;
        }

        if (is_string($concrete) && class_exists($concrete)) {
            return $this->buildClass($concrete, $parameters);
        }

        return $concrete;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function buildClass(string $class, array $parameters = []): object
    {
        if (in_array($class, $this->buildStack, true)) {
            throw CircularDependencyException::detected($class, $this->buildStack);
        }

        $this->buildStack[] = $class;

        try {
            $reflection = new ReflectionClass($class);

            if (! $reflection->isInstantiable()) {
                throw new ContainerException(sprintf('Class "%s" is not instantiable.', $class));
            }

            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                return new $class();
            }

            $dependencies = $this->resolveDependencies($constructor, $parameters);

            return $reflection->newInstanceArgs($dependencies);
        } finally {
            array_pop($this->buildStack);
        }
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return list<mixed>
     */
    private function resolveDependencies(ReflectionMethod|ReflectionFunction $reflection, array $parameters): array
    {
        $dependencies = [];

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $parameters)) {
                $dependencies[] = $parameters[$name];

                continue;
            }

            $dependencies[] = $this->resolveParameter($parameter);
        }

        return $dependencies;
    }

    private function resolveParameter(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
            return $this->make($type->getName());
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new ContainerException(sprintf(
            'Unable to resolve parameter "$%s" for "%s".',
            $parameter->getName(),
            $parameter->getDeclaringClass()?->getName() ?? 'callable',
        ));
    }

    private function cacheResolvedInstance(string $abstract, ?Binding $binding, object $resolved): void
    {
        if ($binding?->isShared() || $binding?->lazy) {
            $this->instances[$abstract] = $resolved;
        }

        if ($binding?->isScoped()) {
            $this->scopedInstances[$abstract] = $resolved;
        }
    }

    private function getAlias(string $abstract): string
    {
        return $this->aliases[$abstract] ?? $abstract;
    }

    private function dropStaleResolution(string $abstract): void
    {
        unset($this->instances[$abstract], $this->scopedInstances[$abstract]);
    }

    private function loadDeferredProviderFor(string $abstract): void
    {
        $provider = $this->deferredProviders[$abstract] ?? null;

        if ($provider === null || $this->isProviderLoaded($provider)) {
            return;
        }

        $provider->register($this);
        $this->markProviderLoaded($provider);
    }

    private function isProviderLoaded(ServiceProviderInterface $provider): bool
    {
        return $this->loadedProviders[$provider::class] ?? false;
    }

    private function markProviderLoaded(ServiceProviderInterface $provider): void
    {
        $this->loadedProviders[$provider::class] = true;
    }

    private function cachePath(): ?string
    {
        return null;
    }
}
