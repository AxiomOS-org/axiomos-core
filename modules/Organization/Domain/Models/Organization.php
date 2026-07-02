<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Organization\Database\Factories\OrganizationFactory;
use Modules\Organization\Domain\Enums\EntityStatus;
use Modules\Organization\Domain\Models\Concerns\HasOrganizationAttributes;

/**
 * Root tenant entity in the AxiomOS hierarchy.
 *
 * @property string $id
 */
final class Organization extends BaseEntityModel
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;
    use HasOrganizationAttributes;
    use SoftDeletes;

    protected $table = 'organizations';

    public static function platformEntityType(): string
    {
        return 'organization.organization';
    }

    /** @var list<string> */
    protected $fillable = [
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

    protected static function newFactory(): OrganizationFactory
    {
        return OrganizationFactory::new();
    }

    /** @return HasMany<Company, $this> */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}
