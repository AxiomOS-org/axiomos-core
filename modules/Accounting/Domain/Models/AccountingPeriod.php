<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class AccountingPeriod extends PlatformEntityModel
{
    protected $table = 'accounting_periods';

    /** @var list<string> */
    protected $fillable = [
        'organization_id','company_id','branch_id','department_id',
        'fiscal_year_id','name','start_date','end_date','is_open','closed_at',
        'created_by','updated_by','deleted_by',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'start_date' => 'date','end_date' => 'date','is_open' => 'bool','closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'accounting.period';
    }
}

