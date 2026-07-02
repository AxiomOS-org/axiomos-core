<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Events;

use Modules\Authentication\Domain\Events\RecordLifecycleEvent;

final class DomainEventRecorder
{
    /** @var list<RecordLifecycleEvent> */
    private static array $events = [];

    public static function record(RecordLifecycleEvent $event): void
    {
        self::$events[] = $event;
    }

    /**
     * @return list<RecordLifecycleEvent>
     */
    public static function all(): array
    {
        return self::$events;
    }

    public static function flush(): void
    {
        self::$events = [];
    }
}
