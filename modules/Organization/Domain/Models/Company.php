<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Organization\Database\Factories\CompanyFactory;
use Modules\Organization\Domain\Enums\EntityStatus;
use Modules\Organization\Domain\Models\Concerns\HasOrganizationAttributes;

/**
 * @property string $id
 * @property string $organization_id
 */
final class Company extends BaseEntityModel
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;
    use HasOrganizationAttributes;
    use SoftDeletes;

    protected $table = 'companies';

    public static function platformEntityType(): string
    {
        return 'organization.company';
    }

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
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

    protected static function newFactory(): CompanyFactory
    {
        return CompanyFactory::new();
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return HasMany<Branch, $this> */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
