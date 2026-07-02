<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class Account extends PlatformEntityModel
{
    protected $table = 'accounting_accounts';

    /** @var list<string> */
    protected $fillable = [
        'organization_id','company_id','branch_id','department_id',
        'parent_account_id','account_code','account_name','account_type','metadata',
        'created_by','updated_by','deleted_by',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'accounting.account';
    }
}

