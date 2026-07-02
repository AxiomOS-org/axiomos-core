<?php

declare(strict_types=1);

namespace App\Platform\Approval;

use App\Platform\Support\PlatformRecord;

final class ApprovalRequest extends PlatformRecord
{
    protected $table = 'universal_approval_requests';

    protected $fillable = [
        'workflow_id',
        'entity_type',
        'entity_id',
        'status',
        'current_step',
        'requested_by',
        'resolved_at',
    ];
}

