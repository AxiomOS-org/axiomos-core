<?php

declare(strict_types=1);

namespace App\Platform\Workflow\Contracts;

interface WorkflowEngineInterface
{
    /**
     * @param list<array{name: string, approver?: string}> $steps
     */
    public function define(string $name, string $entityType, array $steps): string;

    public function start(string $workflowId, string $entityType, string $entityId, ?string $requestedBy = null): string;

    public function approve(string $requestId, ?string $approvedBy = null): string;

    public function reject(string $requestId, ?string $rejectedBy = null, ?string $reason = null): string;

    public function status(string $requestId): string;
}
