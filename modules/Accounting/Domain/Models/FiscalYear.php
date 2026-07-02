<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class FiscalYear extends PlatformEntityModel
{
    protected $table = 'accounting_fiscal_years';

    /** @var list<string> */
    protected $fillable = [
        'organization_id','company_id','branch_id','department_id',
        'name','start_date','end_date','is_closed',
        'created_by','updated_by','deleted_by',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'start_date' => 'date','end_date' => 'date','is_closed' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'accounting.fiscal_year';
    }
}

