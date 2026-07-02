<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class LedgerBalance extends Model { use HasUuids; public $incrementing=false; protected $keyType='string'; protected $table='accounting_ledger_balances'; protected $fillable=['organization_id','company_id','branch_id','department_id','account_id','period_id','currency','opening_debit','opening_credit','period_debit','period_credit','closing_debit','closing_credit']; protected $casts=['opening_debit'=>'decimal:6','opening_credit'=>'decimal:6','period_debit'=>'decimal:6','period_credit'=>'decimal:6','closing_debit'=>'decimal:6','closing_credit'=>'decimal:6','created_at'=>'datetime','updated_at'=>'datetime']; }

