<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Support\Carbon;
use Modules\Authentication\Domain\Models\AuthRateLimit;

final class RateLimitService
{
    public function tooManyAttempts(string $key, int $maxAttempts = 5, int $windowMinutes = 5, int $blockMinutes = 10): bool
    {
        $limit = AuthRateLimit::query()->where('rate_key', $key)->first();

        if ($limit === null) {
            return false;
        }

        if ($limit->blocked_until !== null && $limit->blocked_until->isFuture()) {
            return true;
        }

        if ($limit->window_start === null || $limit->window_start->lt(Carbon::now()->subMinutes($windowMinutes))) {
            $limit->update(['attempts' => 0, 'window_start' => Carbon::now(), 'blocked_until' => null]);

            return false;
        }

        if ((int) $limit->attempts < $maxAttempts) {
            return false;
        }

        $limit->update(['blocked_until' => Carbon::now()->addMinutes($blockMinutes)]);

        return true;
    }

    public function hit(string $key): void
    {
        $limit = AuthRateLimit::query()->where('rate_key', $key)->first();

        if ($limit === null) {
            AuthRateLimit::query()->create([
                'rate_key' => $key,
                'attempts' => 1,
                'window_start' => Carbon::now(),
                'status' => 'active',
            ]);

            return;
        }

        $limit->update([
            'attempts' => ((int) $limit->attempts) + 1,
            'window_start' => $limit->window_start ?? Carbon::now(),
        ]);
    }

    public function clear(string $key): void
    {
        AuthRateLimit::query()->where('rate_key', $key)->delete();
    }
}
