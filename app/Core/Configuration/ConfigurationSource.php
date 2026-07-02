<?php

declare(strict_types=1);

namespace App\Core\Configuration;

/**
 * Identifies which layer supplied a configuration value.
 */
enum ConfigurationSource: string
{
    case File = 'file';
    case Environment = 'environment';
    case Database = 'database';
    case Module = 'module';
    case Plugin = 'plugin';
    case Runtime = 'runtime';

    /**
     * Lower values load first; later sources override earlier ones.
     */
    public function priority(): int
    {
        return match ($this) {
            self::File => 10,
            self::Environment => 20,
            self::Database => 30,
            self::Module => 40,
            self::Plugin => 50,
            self::Runtime => 60,
        };
    }
}
