<?php

declare(strict_types=1);

namespace App\Core\Event\Contracts;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

/**
 * Synchronous, PSR-14 compatible event dispatcher.
 *
 * Invokes every listener resolved by the {@see ListenerProviderInterface},
 * honouring propagation stopping for {@see \Psr\EventDispatcher\StoppableEventInterface}.
 */
interface EventDispatcherInterface extends PsrEventDispatcherInterface
{
    /**
     * Dispatch an event to all registered listeners and return it.
     *
     * @template T of object
     *
     * @param T $event
     *
     * @return T
     */
    public function dispatch(object $event): object;
}
