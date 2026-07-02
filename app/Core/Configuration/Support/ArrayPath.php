<?php

declare(strict_types=1);

namespace App\Core\Configuration\Support;

/**
 * Dot-notation helpers for nested configuration arrays.
 */
final class ArrayPath
{
    /**
     * @param array<string, mixed> $array
     */
    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        $segments = explode('.', $key);
        $current = $array;

        foreach ($segments as $segment) {
            if (! is_array($current) || ! array_key_exists($segment, $current)) {
                return $default;
            }

            $current = $current[$segment];
        }

        return $current;
    }

    /**
     * @param array<string, mixed> $array
     */
    public static function set(array &$array, string $key, mixed $value): void
    {
        if (! str_contains($key, '.')) {
            $array[$key] = $value;

            return;
        }

        $segments = explode('.', $key);
        $current = &$array;

        foreach ($segments as $segment) {
            if (! isset($current[$segment]) || ! is_array($current[$segment])) {
                $current[$segment] = [];
            }

            $current = &$current[$segment];
        }

        $current = $value;
    }

    /**
     * @param array<string, mixed> $array
     */
    public static function has(array $array, string $key): bool
    {
        return self::get($array, $key, '__missing__') !== '__missing__';
    }

    /**
     * @param array<string, mixed> $base
     * @param array<string, mixed> $override
     *
     * @return array<string, mixed>
     */
    public static function merge(array $base, array $override): array
    {
        return array_replace_recursive($base, $override);
    }
}
