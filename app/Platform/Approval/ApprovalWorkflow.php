<?php

declare(strict_types=1);

namespace App\Platform\Approval;

use App\Platform\Support\PlatformRecord;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ApprovalWorkflow extends PlatformRecord
{
    use SoftDeletes;

    protected $table = 'universal_approval_workflows';

    protected $fillable = [
        'entity_type',
        'name',
        'slug',
        'steps',
        'is_active',
    ];
}

