<?php

declare(strict_types=1);

namespace App\Core\Container;

use App\Core\Container\Contracts\ServiceProviderInterface;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Fluent builder for constructing a fully configured {@see Container}.
 */
final class ContainerBuilder
{
    /** @var list<ServiceProviderInterface> */
    private array $providers = [];

    private ?Dispatcher $events = null;

    private ?string $cachePath = null;

    private bool $loadCacheOnBuild = false;

    /**
     * @param list<class-string<ServiceProviderInterface>> $providerClasses
     */
    public function withProviders(array $providerClasses): self
    {
        foreach ($providerClasses as $providerClass) {
            $this->providers[] = new $providerClass();
        }

        return $this;
    }

    public function withProvider(ServiceProviderInterface $provider): self
    {
        $this->providers[] = $provider;

        return $this;
    }

    public function withEventDispatcher(Dispatcher $events): self
    {
        $this->events = $events;

        return $this;
    }

    public function withCache(string $path, bool $loadOnBuild = false): self
    {
        $this->cachePath = $path;
        $this->loadCacheOnBuild = $loadOnBuild;

        return $this;
    }

    public function build(): Container
    {
        $container = new Container($this->events);

        if ($this->cachePath !== null && $this->loadCacheOnBuild) {
            $container->loadCache($this->cachePath);
        }

        foreach ($this->providers as $provider) {
            $container->registerProvider($provider);
        }

        $container->bootProviders();

        return $container;
    }
}
