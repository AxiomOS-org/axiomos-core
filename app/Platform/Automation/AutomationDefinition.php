<?php

declare(strict_types=1);

namespace App\Platform\Automation;

use App\Platform\Automation\Contracts\AutomationTriggerInterface;

final class AutomationDefinition
{
    /**
     * @param list<string> $actionNames
     */
    public function __construct(
        public readonly string $name,
        public readonly AutomationTriggerInterface $trigger,
        public readonly array $actionNames,
    ) {
    }
}
