<?php

declare(strict_types=1);

namespace App\Platform\Approval;

enum ApprovalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}

