<?php

declare(strict_types=1);

namespace App\Core\Event\Support;

/**
 * Immutable description of a single listener registration.
 */
final class ListenerRegistration
{
    /**
     * Framework meta-events are only delivered to explicit listeners, never to
     * wildcard listeners, to prevent per-dispatch amplification.
     */
    private const META_NAMESPACE = 'App\\Core\\Event\\Events\\';

    private bool $exhausted = false;

    /**
     * @param string                 $pattern  Event name or wildcard pattern.
     * @param callable(object): void $listener The listener callable.
     * @param int                    $priority Higher runs first.
     * @param bool                   $once     Remove after a single invocation.
     * @param int                    $sequence Registration order, used to keep sort stable.
     */
    public function __construct(
        public readonly string $pattern,
        public readonly mixed $listener,
        public readonly int $priority,
        public readonly bool $once,
        public readonly int $sequence,
    ) {
    }

    public function isWildcard(): bool
    {
        return str_contains($this->pattern, '*');
    }

    public function matches(string $eventName): bool
    {
        if (! $this->isWildcard()) {
            return $this->pattern === $eventName;
        }

        if (str_starts_with($eventName, self::META_NAMESPACE)) {
            return false;
        }

        if ($this->pattern === '*') {
            return true;
        }

        return str_starts_with($eventName, rtrim($this->pattern, '*'));
    }

    public function isExhausted(): bool
    {
        return $this->exhausted;
    }

    public function markExhausted(): void
    {
        $this->exhausted = true;
    }
}
