<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class JournalLine extends Model { use HasUuids; public $incrementing=false; protected $keyType='string'; protected $table='accounting_journal_lines'; protected $fillable=['organization_id','company_id','branch_id','department_id','journal_id','account_id','cost_center_id','profit_center_id','dimensions','line_currency','line_exchange_rate','debit_amount','credit_amount','functional_debit_amount','functional_credit_amount','description']; protected $casts=['dimensions'=>'array','line_exchange_rate'=>'decimal:8','debit_amount'=>'decimal:6','credit_amount'=>'decimal:6','functional_debit_amount'=>'decimal:6','functional_credit_amount'=>'decimal:6','created_at'=>'datetime','updated_at'=>'datetime']; }

