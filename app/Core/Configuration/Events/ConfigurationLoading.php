<?php

declare(strict_types=1);

namespace App\Core\Configuration\Events;

use App\Core\Configuration\ConfigurationSource;

/**
 * Fired before a single configuration layer is loaded.
 */
final readonly class ConfigurationLoading
{
    public function __construct(public ConfigurationSource $source)
    {
    }
}
