<?php

declare(strict_types=1);

namespace Tests\Unit\Platform;

use App\Platform\Automation\AutomationDefinition;
use App\Platform\Automation\AutomationEngine;
use App\Platform\Automation\AutomationSdk;
use App\Platform\Automation\Contracts\AutomationActionInterface;
use App\Platform\Automation\Contracts\AutomationTriggerInterface;
use PHPUnit\Framework\TestCase;

final class AutomationSdkTest extends TestCase
{
    public function test_it_executes_automation_once_per_idempotency_key(): void
    {
        $engine = new AutomationEngine();
        $sdk = new AutomationSdk($engine);
        $state = new \stdClass();
        $state->executed = 0;

        $trigger = new class implements AutomationTriggerInterface {
            public function name(): string
            {
                return 'entity.created';
            }

            public function matches(array $payload): bool
            {
                return ($payload['type'] ?? '') === 'organization';
            }
        };

        $action = new class($state) implements AutomationActionInterface {
            public function __construct(private \stdClass $state)
            {
            }

            public function name(): string
            {
                return 'log';
            }

            public function execute(array $payload, string $idempotencyKey): void
            {
                ++$this->state->executed;
            }
        };

        $sdk->registerTrigger($trigger);
        $sdk->registerAction($action);
        $sdk->define('org-created', $trigger, ['log']);

        $payload = ['type' => 'organization', 'id' => '1'];
        self::assertSame(1, $sdk->run('entity.created', $payload, 'key-1'));
        self::assertSame(1, $state->executed);
        self::assertSame(0, $sdk->run('entity.created', $payload, 'key-1'));
    }
}
