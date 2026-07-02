<?php

declare(strict_types=1);

namespace App\Core\Event\Contracts;

use App\Core\Event\Support\RecordedEvent;

/**
 * Persists a bounded history of dispatched events for audit and debugging.
 */
interface EventStoreInterface
{
    public function record(RecordedEvent $event): void;

    /**
     * @return list<RecordedEvent>
     */
    public function all(): array;

    public function clear(): void;
}
