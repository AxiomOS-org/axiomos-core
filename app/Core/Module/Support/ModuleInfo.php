<?php

declare(strict_types=1);

namespace App\Core\Module\Support;

/**
 * Lightweight, container-bound marker describing a booted module.
 *
 * Lets other modules discover which peers are active without reaching back into
 * the registry, and gives the boot pipeline something concrete to resolve.
 */
final readonly class ModuleInfo
{
    public function __construct(
        public string $name,
        public string $version,
    ) {
    }
}
