<?php

declare(strict_types=1);

namespace App\Core\Event\Contracts;

use Psr\EventDispatcher\ListenerProviderInterface as PsrListenerProviderInterface;

/**
 * Resolves the ordered set of listeners for a given event.
 *
 * Extends the PSR-14 provider with priority ordering, wildcard registration and
 * one-shot listeners so the AxiomOS event bus can support enterprise routing
 * without breaking interoperability with PSR-14 consumers.
 */
interface ListenerProviderInterface extends PsrListenerProviderInterface
{
    /**
     * Register a listener for an event name or wildcard pattern.
     *
     * @param string                    $eventPattern Event class-string or wildcard (e.g. "App\Events\*", "*").
     * @param callable(object): void    $listener     The listener invoked with the event instance.
     * @param int                       $priority     Higher priority listeners run first.
     * @param bool                      $once         Remove the listener after a single invocation.
     */
    public function listen(string $eventPattern, callable $listener, int $priority = 0, bool $once = false): void;

    /**
     * Remove every listener registered for an exact event name or pattern.
     */
    public function forget(string $eventPattern): void;

    /**
     * Determine whether any listener would handle the given event name.
     */
    public function hasListeners(string $eventName): bool;
}
