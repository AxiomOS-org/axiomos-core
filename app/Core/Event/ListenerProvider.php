<?php

declare(strict_types=1);

namespace App\Core\Event;

use App\Core\Event\Contracts\ListenerProviderInterface;
use App\Core\Event\Support\ListenerRegistration;

/**
 * Priority-ordered, wildcard-aware PSR-14 listener provider.
 *
 * Exact-match listeners are indexed by event name for O(1) lookup; wildcard
 * listeners are scanned per event. Resolved listeners are ordered by descending
 * priority, then registration order for a stable sort.
 */
final class ListenerProvider implements ListenerProviderInterface
{
    /** @var array<string, list<ListenerRegistration>> */
    private array $exact = [];

    /** @var list<ListenerRegistration> */
    private array $wildcard = [];

    private int $sequence = 0;

    public function listen(string $eventPattern, callable $listener, int $priority = 0, bool $once = false): void
    {
        $registration = new ListenerRegistration(
            pattern: $eventPattern,
            listener: $listener,
            priority: $priority,
            once: $once,
            sequence: $this->sequence++,
        );

        if ($registration->isWildcard()) {
            $this->wildcard[] = $registration;

            return;
        }

        $this->exact[$eventPattern][] = $registration;
    }

    public function forget(string $eventPattern): void
    {
        unset($this->exact[$eventPattern]);

        $this->wildcard = array_values(array_filter(
            $this->wildcard,
            static fn (ListenerRegistration $registration): bool => $registration->pattern !== $eventPattern,
        ));
    }

    public function hasListeners(string $eventName): bool
    {
        if (isset($this->exact[$eventName]) && $this->exact[$eventName] !== []) {
            return true;
        }

        foreach ($this->wildcard as $registration) {
            if ($registration->matches($eventName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return iterable<callable(object): void>
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->registrationsForEvent($event::class) as $registration) {
            if ($registration->once) {
                $registration->markExhausted();
                $this->removeRegistration($registration);
            }

            yield $registration->listener;
        }
    }

    /**
     * Resolve every matching, non-exhausted registration, ordered for dispatch.
     *
     * @return list<ListenerRegistration>
     */
    private function registrationsForEvent(string $eventName): array
    {
        $matched = [];

        foreach ($this->exact[$eventName] ?? [] as $registration) {
            if (! $registration->isExhausted()) {
                $matched[] = $registration;
            }
        }

        foreach ($this->wildcard as $registration) {
            if (! $registration->isExhausted() && $registration->matches($eventName)) {
                $matched[] = $registration;
            }
        }

        usort(
            $matched,
            static fn (ListenerRegistration $a, ListenerRegistration $b): int =>
                $b->priority <=> $a->priority ?: $a->sequence <=> $b->sequence,
        );

        return $matched;
    }

    private function removeRegistration(ListenerRegistration $target): void
    {
        foreach ($this->exact as $eventName => $registrations) {
            $this->exact[$eventName] = array_values(array_filter(
                $registrations,
                static fn (ListenerRegistration $registration): bool => $registration !== $target,
            ));
        }

        $this->wildcard = array_values(array_filter(
            $this->wildcard,
            static fn (ListenerRegistration $registration): bool => $registration !== $target,
        ));
    }
}
