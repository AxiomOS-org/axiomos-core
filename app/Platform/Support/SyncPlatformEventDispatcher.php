<?php

declare(strict_types=1);

namespace App\Platform\Support;

/**
 * Best-effort synchronous dispatcher.
 *
 * The current core runtime does not require a dedicated domain event bus for
 * platform boot/health, so dispatch is a no-op unless wired by application code.
 */
final class SyncPlatformEventDispatcher implements PlatformEventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        // Intentionally empty: platform events are optional in core tests.
    }
}

