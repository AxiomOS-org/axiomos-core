<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Models;
use App\Platform\Support\PlatformEntityModel;
final class Document extends PlatformEntityModel {
    public const STATUS_DRAFT='draft'; public const STATUS_PENDING_APPROVAL='pending_approval'; public const STATUS_APPROVED='approved'; public const STATUS_POSTED='posted'; public const STATUS_LOCKED='locked'; public const STATUS_CANCELLED='cancelled'; public const STATUS_REVERSED='reversed';
    protected $table='accounting_documents';
    protected $fillable=['organization_id','company_id','branch_id','department_id','voucher_type_id','fiscal_year_id','period_id','source_module','source_document_type','source_document_id','document_number','document_date','posting_date','currency','exchange_rate','status','description','metadata','approved_by','approved_at','posted_by','posted_at','created_by','updated_by','deleted_by'];
    protected $casts=['document_date'=>'date','posting_date'=>'date','exchange_rate'=>'decimal:8','metadata'=>'array','approved_at'=>'datetime','posted_at'=>'datetime','created_at'=>'datetime','updated_at'=>'datetime','deleted_at'=>'datetime'];
    public static function entityType(): string { return 'accounting.document'; }
}

