<?php

declare(strict_types=1);

namespace App\Core\Http\Events;

/**
 * Dispatched at the start of every HTTP request, after the kernel is ready.
 */
final readonly class RequestReceived
{
    public function __construct(
        public string $method,
        public string $path,
        public float $receivedAt,
    ) {
    }
}
