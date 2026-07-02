<?php

declare(strict_types=1);

namespace App\Platform\Workflow;

use App\Platform\Support\PlatformEntityInterface;

final class WorkflowEntityStub implements PlatformEntityInterface
{
    public function __construct(
        private readonly string $entityType,
        private readonly string $entityId,
    ) {
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }
}
