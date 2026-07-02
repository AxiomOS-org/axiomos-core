<?php

declare(strict_types=1);

namespace App\Platform\Automation;

use App\Platform\Automation\Contracts\AutomationActionInterface;
use App\Platform\Automation\Contracts\AutomationTriggerInterface;
use RuntimeException;

final class AutomationEngine
{
    /** @var array<string, AutomationTriggerInterface> */
    private array $triggers = [];

    /** @var array<string, AutomationActionInterface> */
    private array $actions = [];

    /** @var list<AutomationDefinition> */
    private array $definitions = [];

    /** @var array<string, true> */
    private array $processedKeys = [];

    public function registerTrigger(AutomationTriggerInterface $trigger): void
    {
        $this->triggers[$trigger->name()] = $trigger;
    }

    public function registerAction(AutomationActionInterface $action): void
    {
        $this->actions[$action->name()] = $action;
    }

    public function register(AutomationDefinition $definition): void
    {
        $this->definitions[] = $definition;
    }

    public function dispatch(string $eventName, array $payload, string $idempotencyKey): int
    {
        if (isset($this->processedKeys[$idempotencyKey])) {
            return 0;
        }

        $executed = 0;

        foreach ($this->definitions as $definition) {
            if ($definition->trigger->name() !== $eventName) {
                continue;
            }

            if (! $definition->trigger->matches($payload)) {
                continue;
            }

            foreach ($definition->actionNames as $actionName) {
                if (! isset($this->actions[$actionName])) {
                    throw new RuntimeException("Automation action not registered: {$actionName}");
                }

                $this->actions[$actionName]->execute($payload, $idempotencyKey);
                ++$executed;
            }
        }

        $this->processedKeys[$idempotencyKey] = true;

        return $executed;
    }
}
