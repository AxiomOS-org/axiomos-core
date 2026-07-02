<?php

declare(strict_types=1);

namespace App\Core\Event\Contracts;

/**
 * A single-responsibility event listener.
 *
 * Implementations are invokable via {@see handle()} and may be registered
 * directly with the event bus as callables.
 */
interface EventListenerInterface
{
    public function handle(object $event): void;
}
