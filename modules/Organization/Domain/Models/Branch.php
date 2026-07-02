<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Organization\Database\Factories\BranchFactory;
use Modules\Organization\Domain\Enums\EntityStatus;
use Modules\Organization\Domain\Models\Concerns\HasOrganizationAttributes;

/**
 * @property string $id
 * @property string $company_id
 */
final class Branch extends BaseEntityModel
{
    /** @use HasFactory<BranchFactory> */
    use HasFactory;
    use HasOrganizationAttributes;
    use SoftDeletes;

    protected $table = 'branches';

    public static function platformEntityType(): string
    {
        return 'organization.branch';
    }

    /** @var list<string> */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
        'slug',
        'logo',
        'status',
        'timezone',
        'currency',
        'locale',
        'country',
        'settings',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'settings' => 'array',
        'status' => EntityStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function newFactory(): BranchFactory
    {
        return BranchFactory::new();
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** @return HasMany<Department, $this> */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
