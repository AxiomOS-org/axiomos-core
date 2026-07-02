<?php

declare(strict_types=1);

namespace App\Platform\Automation\Contracts;

interface AutomationTriggerInterface
{
    public function name(): string;

    public function matches(array $payload): bool;
}
