<?php

declare(strict_types=1);

namespace App\Core\Module;

use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Resolves module boot order from declared dependencies.
 *
 * Order:
 * 1. All dependency edges are satisfied (topological sort by name).
 * 2. Then ascending priority.
 * 3. Then alphabetical name for stability.
 */
final class ModuleDependencyResolver
{
    /**
     * @param Collection<int, ModuleManifest> $manifests
     *
     * @return Collection<int, ModuleManifest>
     */
    public static function resolve(Collection $manifests): Collection
    {
        // First compute a dependency-only order using module names.
        $byName = [];
        foreach ($manifests as $manifest) {
            $byName[$manifest->name] = $manifest;
        }

        $inDegree = [];
        $graph = [];

        foreach ($manifests as $manifest) {
            $name = $manifest->name;
            $inDegree[$name] ??= 0;
            $graph[$name] ??= [];

            foreach ($manifest->dependencies as $dep) {
                // Dependencies have already been validated as resolvable.
                $graph[$dep] ??= [];
                $inDegree[$name] = ($inDegree[$name] ?? 0) + 1;
                $graph[$dep][] = $name;
            }
        }

        // Kahn's algorithm for topological sorting by name.
        $queue = [];
        foreach ($inDegree as $name => $degree) {
            if ($degree === 0) {
                $queue[] = $name;
            }
        }

        sort($queue);

        $orderedNames = [];

        while ($queue !== []) {
            $current = array_shift($queue);
            $orderedNames[] = $current;

            foreach ($graph[$current] ?? [] as $next) {
                $inDegree[$next]--;
                if ($inDegree[$next] === 0) {
                    $queue[] = $next;
                }
            }

            sort($queue);
        }

        if (count($orderedNames) !== count($byName)) {
            throw new RuntimeException('Circular module dependency detected.');
        }

        // Map manifests by name for fast lookup.
        $priorityBuckets = [];

        foreach ($orderedNames as $name) {
            $manifest = $byName[$name];
            $priorityBuckets[$manifest->priority][] = $manifest;
        }

        ksort($priorityBuckets);

        $result = new Collection();

        foreach ($priorityBuckets as $bucket) {
            usort(
                $bucket,
                static fn (ModuleManifest $a, ModuleManifest $b): int => strcmp($a->name, $b->name),
            );

            foreach ($bucket as $manifest) {
                $result->push($manifest);
            }
        }

        return $result->values();
    }
}

