<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Models;

use App\Platform\Support\PlatformEntityModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EmployeeProfile extends PlatformEntityModel
{
    protected $table = 'employee_profiles';

    /** @var list<string> */
    protected $fillable = [
        'identity_id',
        'organization_id',
        'employee_number',
        'job_title',
        'department_id',
        'hire_date',
        'status',
        'metadata',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'metadata' => 'array',
        'hire_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'identity.employee_profile';
    }

    /** @return BelongsTo<Identity, $this> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class);
    }
}
