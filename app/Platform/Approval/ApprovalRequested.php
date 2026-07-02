<?php

declare(strict_types=1);

namespace App\Platform\Approval;

use App\Platform\Support\PlatformEntityInterface;

final class ApprovalRequested
{
    public function __construct(
        public readonly PlatformEntityInterface $entity,
        public readonly string $workflowId,
        public readonly ?string $requestedBy = null,
    ) {
    }
}

