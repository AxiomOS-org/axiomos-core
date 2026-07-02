<?php

declare(strict_types=1);

namespace Modules\Authorization\Application\Events;

final class DomainEventRecorder
{
    /** @var list<object> */
    private static array $events = [];

    public static function record(object $event): void
    {
        self::$events[] = $event;
    }

    /** @return list<object> */
    public static function release(): array
    {
        $events = self::$events;
        self::$events = [];

        return $events;
    }
}
