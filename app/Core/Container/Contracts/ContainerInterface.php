<?php

declare(strict_types=1);

namespace App\Core\Container\Contracts;

use App\Core\Container\Scope;
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Enterprise service container contract (PSR-11 compatible).
 */
interface ContainerInterface extends PsrContainerInterface
{
    public function bind(string $abstract, mixed $concrete = null, Scope $scope = Scope::Transient): void;

    public function singleton(string $abstract, mixed $concrete = null): void;

    public function scoped(string $abstract, mixed $concrete = null): void;

    public function instance(string $abstract, object $instance): void;

    public function alias(string $alias, string $abstract): void;

    /**
     * @param list<string> $tags
     */
    public function tag(string $abstract, array $tags): void;

    /**
     * @return list<object>
     */
    public function tagged(string $tag): array;

    public function lazy(string $abstract, mixed $concrete = null): void;

    public function make(string $abstract, array $parameters = []): mixed;

    public function call(callable|array|string $callback, array $parameters = []): mixed;

    public function registerProvider(ServiceProviderInterface $provider): void;

    public function bootProviders(): void;

    public function flushScoped(): void;

    public function cache(?string $path = null): void;

    public function loadCache(string $path): bool;

    public function clearCache(string $path): void;
}
