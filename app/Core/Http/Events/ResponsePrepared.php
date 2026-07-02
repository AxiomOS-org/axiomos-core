<?php

declare(strict_types=1);

namespace App\Core\Http\Events;

/**
 * Dispatched once a response has been produced for the current request.
 */
final readonly class ResponsePrepared
{
    public function __construct(
        public int $statusCode,
        public float $durationMs,
    ) {
    }
}
