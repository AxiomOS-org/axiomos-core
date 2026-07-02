<?php

declare(strict_types=1);

namespace App\Platform\Support;

/**
 * Dispatches platform domain events.
 */
interface PlatformEventDispatcherInterface
{
    public function dispatch(object $event): void;
}

