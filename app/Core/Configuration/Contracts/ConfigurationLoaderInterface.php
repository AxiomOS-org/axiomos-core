<?php

declare(strict_types=1);

namespace App\Core\Configuration\Contracts;

use App\Core\Configuration\ConfigurationSource;

/**
 * Loads configuration from a single layer (env, files, database, etc.).
 */
interface ConfigurationLoaderInterface
{
    public function source(): ConfigurationSource;

  /**
   * @return array<string, mixed> Nested configuration array.
   */
    public function load(): array;

    public function supports(): bool;
}
