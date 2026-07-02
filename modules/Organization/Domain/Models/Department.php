<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Organization\Database\Factories\DepartmentFactory;
use Modules\Organization\Domain\Enums\EntityStatus;
use Modules\Organization\Domain\Models\Concerns\HasOrganizationAttributes;

/**
 * @property string $id
 * @property string $branch_id
 * @property string|null $parent_id
 */
final class Department extends BaseEntityModel
{
    /** @use HasFactory<DepartmentFactory> */
    use HasFactory;
    use HasOrganizationAttributes;
    use SoftDeletes;

    protected $table = 'departments';

    public static function platformEntityType(): string
    {
        return 'organization.department';
    }

    /** @var list<string> */
    protected $fillable = [
        'branch_id',
        'parent_id',
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

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }

    /** @return BelongsTo<Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return BelongsTo<Department, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<Department, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
