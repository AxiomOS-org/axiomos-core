<?php

declare(strict_types=1);

namespace App\Core\Http\Health;

/**
 * Severity of a health check, ordered from healthy to unhealthy.
 */
enum HealthStatus: string
{
    case Ok = 'ok';
    case Degraded = 'degraded';
    case Down = 'down';

    /**
     * Rank used to compute the worst status across many checks.
     */
    public function severity(): int
    {
        return match ($this) {
            self::Ok => 0,
            self::Degraded => 1,
            self::Down => 2,
        };
    }

    /**
     * Return the more severe of two statuses.
     */
    public function worst(self $other): self
    {
        return $other->severity() > $this->severity() ? $other : $this;
    }
}
