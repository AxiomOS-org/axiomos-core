<?php

declare(strict_types=1);

namespace App\Platform\Integration\Contracts;

interface WebhookHandlerInterface
{
    public function event(): string;

    public function handle(array $payload): void;
}
