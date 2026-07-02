<?php

declare(strict_types=1);

namespace App\Core\Configuration\Events;

/**
 * Fired after configuration has been successfully reloaded.
 */
final readonly class ConfigurationReloaded
{
    /**
     * @param array<string, mixed> $configuration
     */
    public function __construct(public array $configuration)
    {
    }
}
