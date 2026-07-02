<?php

declare(strict_types=1);

namespace App\Core\Container\Events;

/**
 * Fired immediately before a service is resolved from the container.
 */
final readonly class ContainerResolving
{
    public function __construct(
        public string $abstract,
        public array $parameters,
    ) {
    }
}
