<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class VoucherType extends PlatformEntityModel
{
    protected $table = 'accounting_voucher_types';

    /** @var list<string> */
    protected $fillable = [
        'organization_id','company_id','branch_id','department_id',
        'code','name','series_pattern','auto_approve','is_active',
        'created_by','updated_by','deleted_by',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'auto_approve' => 'bool','is_active' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'accounting.voucher_type';
    }
}

