<?php

declare(strict_types=1);

namespace App\Platform\Automation;

use App\Platform\Automation\Contracts\AutomationActionInterface;
use App\Platform\Automation\Contracts\AutomationTriggerInterface;

final class AutomationSdk
{
    public function __construct(
        private readonly AutomationEngine $engine,
    ) {
    }

    public function registerTrigger(AutomationTriggerInterface $trigger): void
    {
        $this->engine->registerTrigger($trigger);
    }

    public function registerAction(AutomationActionInterface $action): void
    {
        $this->engine->registerAction($action);
    }

    /**
     * @param list<string> $actionNames
     */
    public function define(string $name, AutomationTriggerInterface $trigger, array $actionNames): void
    {
        $this->engine->register(new AutomationDefinition($name, $trigger, $actionNames));
    }

    public function run(string $eventName, array $payload, string $idempotencyKey): int
    {
        return $this->engine->dispatch($eventName, $payload, $idempotencyKey);
    }
}
