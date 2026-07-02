<?php

declare(strict_types=1);

namespace App\Core\Event\Contracts;

/**
 * Registers a related group of listeners in one place.
 *
 * The returned map keys are event names (or wildcard patterns); values are
 * either a callable, an [callable, priority] pair, or a list thereof.
 */
interface EventSubscriberInterface
{
    /**
     * @return array<string, callable|array{0: callable, 1: int}|list<callable|array{0: callable, 1: int}>>
     */
    public function subscribe(): array;
}
