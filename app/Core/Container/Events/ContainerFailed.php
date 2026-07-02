<?php

declare(strict_types=1);

namespace App\Core\Container\Events;

use Throwable;

/**
 * Fired when service resolution fails.
 */
final readonly class ContainerFailed
{
    public function __construct(
        public string $abstract,
        public Throwable $exception,
    ) {
    }
}
