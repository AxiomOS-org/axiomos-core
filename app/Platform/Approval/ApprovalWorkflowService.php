<?php

declare(strict_types=1);

namespace App\Platform\Approval;

use App\Platform\Support\PlatformEntityInterface;

final class ApprovalWorkflowService
{
    public function createWorkflow(
        string $entityType,
        string $name,
        ?string $slug = null,
        array $steps = [],
    ): ApprovalWorkflow {
        $workflow = new ApprovalWorkflow();
        $workflow->entity_type = $entityType;
        $workflow->name = $name;
        $workflow->slug = $slug ?? $name;
        $workflow->steps = $steps ?: null;
        $workflow->save();

        return $workflow;
    }

    public function requestApproval(
        PlatformEntityInterface $entity,
        ApprovalWorkflow $workflow,
        ?string $requestedBy = null,
    ): ApprovalRequest {
        $request = new ApprovalRequest();
        $request->workflow_id = (string) $workflow->getKey();
        $request->entity_type = $entity->getEntityType();
        $request->entity_id = $entity->getEntityId();
        $request->status = ApprovalStatus::PENDING->value;
        $request->current_step = 0;
        $request->requested_by = $requestedBy;
        $request->save();

        return $request;
    }
}

