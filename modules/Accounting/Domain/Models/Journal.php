<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Models;
use App\Platform\Support\PlatformEntityModel;
final class Journal extends PlatformEntityModel {
    protected $table='accounting_journals';
    protected $fillable=['organization_id','company_id','branch_id','department_id','document_id','voucher_type_id','fiscal_year_id','period_id','journal_number','posting_date','currency','exchange_rate','debit_total','credit_total','status','posted_by','posted_at','signature_hash','created_by','updated_by','deleted_by'];
    protected $casts=['posting_date'=>'date','exchange_rate'=>'decimal:8','debit_total'=>'decimal:6','credit_total'=>'decimal:6','posted_at'=>'datetime','created_at'=>'datetime','updated_at'=>'datetime','deleted_at'=>'datetime'];
    public static function entityType(): string { return 'accounting.journal'; }
}

