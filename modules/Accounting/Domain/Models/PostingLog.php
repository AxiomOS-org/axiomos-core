<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Models;

use App\Platform\Support\PlatformEntityModel;

final class PostingLog extends PlatformEntityModel
{
    protected $table = 'accounting_posting_log';

    /** @var list<string> */
    protected $fillable = [
        'organization_id','company_id','branch_id','department_id',
        'idempotency_key','source_module','source_document_type','source_document_id','document_id','journal_id','status','error_message','request_payload','response_payload','processed_at',
        'created_by','updated_by','deleted_by',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'request_payload' => 'array','response_payload' => 'array','processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function entityType(): string
    {
        return 'accounting.posting_log';
    }
}

