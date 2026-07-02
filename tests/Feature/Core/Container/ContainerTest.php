<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Container;

use App\Core\Container\Container;
use App\Core\Container\ContainerBuilder;
use App\Core\Container\Contracts\BootableProviderInterface;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Container\Contracts\DeferredProviderInterface;
use App\Core\Container\Events\ContainerFailed;
use App\Core\Container\Events\ContainerResolved;
use App\Core\Container\Events\ContainerResolving;
use App\Core\Container\Exceptions\CircularDependencyException;
use App\Core\Container\Exceptions\NotFoundException;
use App\Core\Container\ServiceProvider;
use Illuminate\Events\Dispatcher;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface as PsrContainerInterface;

final class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        ExpensiveService::$constructed = 0;
        $this->container = new Container();
    }

    public function test_it_is_psr_11_compatible(): void
    {
        self::assertInstanceOf(PsrContainerInterface::class, $this->container);
    }

    public function test_it_resolves_transient_bindings(): void
    {
        $this->container->bind(Clock::class, Clock::class);

        self::assertNotSame($this->container->make(Clock::class), $this->container->make(Clock::class));
    }

    public function test_it_resolves_singleton_bindings(): void
    {
        $this->container->singleton(Clock::class, Clock::class);

        self::assertSame($this->container->make(Clock::class), $this->container->make(Clock::class));
    }

    public function test_it_resolves_scoped_bindings(): void
    {
        $this->container->scoped(Clock::class, Clock::class);

        $first = $this->container->make(Clock::class);
        $second = $this->container->make(Clock::class);

        self::assertSame($first, $second);

        $this->container->flushScoped();

        self::assertNotSame($first, $this->container->make(Clock::class));
    }

    public function test_it_registers_instances(): void
    {
        $clock = new Clock();
        $this->container->instance(Clock::class, $clock);

        self::assertSame($clock, $this->container->make(Clock::class));
    }

    public function test_it_resolves_aliases(): void
    {
        $this->container->singleton(Clock::class, Clock::class);
        $this->container->alias('clock', Clock::class);

        self::assertSame($this->container->make(Clock::class), $this->container->get('clock'));
    }

    public function test_it_resolves_tagged_services(): void
    {
        $this->container->singleton(Clock::class, Clock::class);
        $this->container->singleton(Stopwatch::class, Stopwatch::class);
        $this->container->tag(Clock::class, ['timing']);
        $this->container->tag(Stopwatch::class, ['timing']);

        $services = $this->container->tagged('timing');

        self::assertCount(2, $services);
        self::assertContainsOnlyInstancesOf(TimingContract::class, $services);
    }

    public function test_it_auto_wires_constructor_dependencies(): void
    {
        $this->container->singleton(Clock::class, Clock::class);

        $service = $this->container->make(ServiceWithClock::class);

        self::assertInstanceOf(Clock::class, $service->clock);
    }

    public function test_it_resolves_interface_bindings(): void
    {
        $this->container->singleton(TimingContract::class, Clock::class);

        self::assertInstanceOf(Clock::class, $this->container->make(TimingContract::class));
    }

    public function test_it_detects_circular_dependencies(): void
    {
        $this->expectException(CircularDependencyException::class);

        $this->container->make(CircularA::class);
    }

    public function test_it_lazy_loads_singletons(): void
    {
        $this->container->lazy(ExpensiveService::class, ExpensiveService::class);

        $service = $this->container->make(ExpensiveService::class);

        self::assertSame(1, ExpensiveService::$constructed);
        self::assertSame($service, $this->container->make(ExpensiveService::class));
        self::assertSame(1, ExpensiveService::$constructed);
    }

    public function test_it_registers_and_boots_service_providers(): void
    {
        $provider = new TestServiceProvider();

        $this->container->registerProvider($provider);
        $this->container->bootProviders();

        self::assertTrue($provider->registered);
        self::assertTrue($provider->booted);
        self::assertInstanceOf(Clock::class, $this->container->make('provider.clock'));
    }

    public function test_it_defers_provider_registration_until_needed(): void
    {
        $provider = new DeferredTestProvider();
        $this->container->registerProvider($provider);

        self::assertFalse($provider->registered);

        $this->container->make('deferred.service');

        self::assertTrue($provider->registered);
    }

    public function test_container_builder_discovers_providers(): void
    {
        $container = (new ContainerBuilder())
            ->withProvider(new TestServiceProvider())
            ->build();

        self::assertInstanceOf(Clock::class, $container->make('provider.clock'));
    }

    public function test_it_caches_and_loads_binding_metadata(): void
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'axiomos_container_' . uniqid('', true) . '.php';

        $this->container->singleton(Clock::class, Clock::class);
        $this->container->alias('clock', Clock::class);
        $this->container->tag(Clock::class, ['timing']);
        $this->container->cache($path);

        $fresh = new Container();
        self::assertTrue($fresh->loadCache($path));
        self::assertInstanceOf(Clock::class, $fresh->make('clock'));

        $fresh->clearCache($path);

        self::assertFileDoesNotExist($path);
    }

    public function test_it_fires_resolution_events(): void
    {
        $events = new Dispatcher();
        $container = new Container($events);
        $container->singleton(Clock::class, Clock::class);

        $captured = [];
        $events->listen('*', static function (string $event) use (&$captured): void {
            $captured[] = $event;
        });

        $container->make(Clock::class);

        self::assertContains(ContainerResolving::class, $captured);
        self::assertContains(ContainerResolved::class, $captured);
    }

    public function test_it_fires_failure_events(): void
    {
        $events = new Dispatcher();
        $container = new Container($events);

        $failed = 0;
        $events->listen(ContainerFailed::class, static function () use (&$failed): void {
            $failed++;
        });

        try {
            $container->make('missing.service');
        } catch (NotFoundException) {
            self::assertSame(1, $failed);
        }
    }

    public function test_it_invokes_callables_with_dependency_injection(): void
    {
        $this->container->singleton(Clock::class, Clock::class);

        $result = $this->container->call(static function (Clock $clock): string {
            return $clock::class;
        });

        self::assertSame(Clock::class, $result);
    }

    public function test_it_throws_when_service_is_missing(): void
    {
        $this->expectException(NotFoundException::class);

        $this->container->make('missing.service');
    }
}

interface TimingContract
{
}

final class Clock implements TimingContract
{
}

final class Stopwatch implements TimingContract
{
}

final class ServiceWithClock
{
    public function __construct(public readonly Clock $clock)
    {
    }
}

final class CircularA
{
    public function __construct(public readonly CircularB $b)
    {
    }
}

final class CircularB
{
    public function __construct(public readonly CircularA $a)
    {
    }
}

final class ExpensiveService
{
    public static int $constructed = 0;

    public function __construct()
    {
        self::$constructed++;
    }

    protected function __clone()
    {
    }
}

final class TestServiceProvider extends ServiceProvider implements BootableProviderInterface
{
    public bool $registered = false;

    public bool $booted = false;

    public function register(ContainerInterface $container): void
    {
        $this->registered = true;
        $container->singleton('provider.clock', Clock::class);
    }

    public function boot(ContainerInterface $container): void
    {
        $this->booted = true;
    }
}

final class DeferredTestProvider extends ServiceProvider implements DeferredProviderInterface
{
    public bool $registered = false;

    public function register(ContainerInterface $container): void
    {
        $this->registered = true;
        $container->singleton('deferred.service', Clock::class);
    }

    public function provides(): array
    {
        return ['deferred.service'];
    }
}
