<?php

declare(strict_types=1);

namespace App\Platform\Automation\Contracts;

interface AutomationActionInterface
{
    public function name(): string;

    public function execute(array $payload, string $idempotencyKey): void;
}
