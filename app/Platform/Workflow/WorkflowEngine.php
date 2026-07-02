<?php

declare(strict_types=1);

namespace App\Platform\Workflow;

use App\Platform\Approval\ApprovalStatus;
use App\Platform\Approval\ApprovalWorkflow;
use App\Platform\Approval\ApprovalWorkflowService;
use App\Platform\Workflow\Contracts\WorkflowEngineInterface;
use RuntimeException;

final class WorkflowEngine implements WorkflowEngineInterface
{
    public function __construct(
        private readonly ApprovalWorkflowService $approvalService,
    ) {
    }

    public function define(string $name, string $entityType, array $steps): string
    {
        $workflow = $this->approvalService->createWorkflow(
            entityType: $entityType,
            name: $name,
            steps: $steps,
        );

        return (string) $workflow->getKey();
    }

    public function start(string $workflowId, string $entityType, string $entityId, ?string $requestedBy = null): string
    {
        $workflow = ApprovalWorkflow::query()->find($workflowId);

        if ($workflow === null) {
            throw new RuntimeException("Workflow not found: {$workflowId}");
        }

        $entity = new WorkflowEntityStub($entityType, $entityId);
        $request = $this->approvalService->requestApproval($entity, $workflow, $requestedBy);

        return (string) $request->getKey();
    }

    public function approve(string $requestId, ?string $approvedBy = null): string
    {
        $request = $this->findRequest($requestId);
        $request->status = ApprovalStatus::APPROVED->value;
        $request->resolved_at = now();
        $request->save();

        return ApprovalStatus::APPROVED->value;
    }

    public function reject(string $requestId, ?string $rejectedBy = null, ?string $reason = null): string
    {
        $request = $this->findRequest($requestId);
        $request->status = ApprovalStatus::REJECTED->value;
        $request->resolved_at = now();
        $request->save();

        return ApprovalStatus::REJECTED->value;
    }

    public function status(string $requestId): string
    {
        return $this->findRequest($requestId)->status;
    }

    private function findRequest(string $requestId): \App\Platform\Approval\ApprovalRequest
    {
        $request = \App\Platform\Approval\ApprovalRequest::query()->find($requestId);

        if ($request === null) {
            throw new RuntimeException("Workflow request not found: {$requestId}");
        }

        return $request;
    }
}
