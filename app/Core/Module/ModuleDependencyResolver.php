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

        $sortQueue = static function () use (&$queue, $byName): void {
            usort(
                $queue,
                static function (string $a, string $b) use ($byName): int {
                    $priority = $byName[$a]->priority <=> $byName[$b]->priority;

                    return $priority !== 0 ? $priority : strcmp($a, $b);
                },
            );
        };

        $sortQueue();

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

            $sortQueue();
        }

        if (count($orderedNames) !== count($byName)) {
            throw new RuntimeException('Circular module dependency detected.');
        }

        $result = new Collection();

        foreach ($orderedNames as $name) {
            $result->push($byName[$name]);
        }

        return $result->values();
    }
}

