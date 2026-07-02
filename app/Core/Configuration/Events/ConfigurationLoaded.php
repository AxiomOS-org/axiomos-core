<?php

declare(strict_types=1);

namespace App\Core\Configuration\Events;

/**
 * Fired after all configuration layers have been merged and validated.
 */
final readonly class ConfigurationLoaded
{
    /**
     * @param array<string, mixed> $configuration
     */
    public function __construct(public array $configuration)
    {
    }
}
