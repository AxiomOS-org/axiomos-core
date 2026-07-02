<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class Dimension extends PlatformEntityModel
{
    protected $table = 'accounting_dimensions';

    /** @var list<string> */
    protected $fillable = [
        'organization_id','company_id','branch_id','department_id',
        'dimension_type','code','name','is_active','metadata',
        'created_by','updated_by','deleted_by',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'is_active' => 'bool','metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'accounting.dimension';
    }
}

