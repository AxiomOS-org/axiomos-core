<?php

declare(strict_types=1);

namespace App\Core\Container;

/**
 * Immutable description of a single container binding.
 */
final readonly class Binding
{
    /**
     * @param string               $abstract  The service identifier (class or interface).
     * @param mixed                $concrete  Class name, closure, or pre-built instance factory.
     * @param Scope                $scope     Lifetime scope for resolved instances.
     * @param bool                 $lazy      Defer instantiation until first access.
     * @param list<string>         $tags      Tags for grouped resolution.
     */
    public function __construct(
        public string $abstract,
        public mixed $concrete,
        public Scope $scope = Scope::Transient,
        public bool $lazy = false,
        public array $tags = [],
    ) {
    }

    public function isShared(): bool
    {
        return $this->scope === Scope::Singleton;
    }

    public function isScoped(): bool
    {
        return $this->scope === Scope::Scoped;
    }
}
