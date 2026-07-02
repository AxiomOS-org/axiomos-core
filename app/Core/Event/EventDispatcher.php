<?php

declare(strict_types=1);

namespace App\Core\Event;

use App\Core\Event\Contracts\EventDispatcherInterface;
use App\Core\Event\Contracts\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Synchronous PSR-14 dispatcher.
 *
 * Invokes each resolved listener in order, honouring propagation stopping for
 * {@see StoppableEventInterface}. It performs no queueing, history or metrics —
 * those concerns belong to {@see EventBus}, keeping this class single-purpose.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(private readonly ListenerProviderInterface $listeners)
    {
    }

    public function dispatch(object $event): object
    {
        $stoppable = $event instanceof StoppableEventInterface;

        foreach ($this->listeners->getListenersForEvent($event) as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }
}
