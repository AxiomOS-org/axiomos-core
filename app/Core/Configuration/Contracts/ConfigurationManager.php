<?php

declare(strict_types=1);

namespace App\Core\Configuration\Contracts;

use App\Core\Configuration\ConfigurationSource;

/**
 * Enterprise configuration manager consumed by the kernel and modules.
 */
interface ConfigurationManager
{
    public function load(): void;

    public function reload(): void;

    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): void;

    public function has(string $key): bool;

    /**
     * @return array<string, mixed>
     */
    public function all(): array;

    public function sourceOf(string $key): ?ConfigurationSource;

    public function cache(?string $path = null): void;

    public function loadCache(string $path): bool;

    public function clearCache(string $path): void;
}
