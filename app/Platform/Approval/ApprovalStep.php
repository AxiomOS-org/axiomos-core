<?php

declare(strict_types=1);

namespace App\Platform\Approval;

use App\Platform\Support\PlatformRecord;

final class ApprovalStep extends PlatformRecord
{
    protected $table = 'universal_approval_steps';

    protected $fillable = [
        'request_id',
        'step_order',
        'status',
        'approver_id',
        'comment',
        'acted_at',
    ];
}

