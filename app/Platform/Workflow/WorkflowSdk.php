<?php

declare(strict_types=1);

namespace App\Platform\Workflow;

use App\Platform\Workflow\Contracts\WorkflowEngineInterface;

final class WorkflowSdk
{
    public function __construct(
        private readonly WorkflowEngineInterface $engine,
    ) {
    }

    /**
     * @param list<array{name: string, approver?: string}> $steps
     */
    public function createApprovalFlow(string $name, string $entityType, array $steps): string
    {
        return $this->engine->define($name, $entityType, $steps);
    }

    public function submit(string $workflowId, string $entityType, string $entityId, ?string $requestedBy = null): string
    {
        return $this->engine->start($workflowId, $entityType, $entityId, $requestedBy);
    }

    public function approve(string $requestId, ?string $approvedBy = null): string
    {
        return $this->engine->approve($requestId, $approvedBy);
    }

    public function reject(string $requestId, ?string $rejectedBy = null, ?string $reason = null): string
    {
        return $this->engine->reject($requestId, $rejectedBy, $reason);
    }
}
