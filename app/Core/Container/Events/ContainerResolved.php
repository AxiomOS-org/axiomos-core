<?php

declare(strict_types=1);

namespace App\Core\Container\Events;

/**
 * Fired after a service has been successfully resolved.
 */
final readonly class ContainerResolved
{
    public function __construct(
        public string $abstract,
        public object $instance,
    ) {
    }
}
