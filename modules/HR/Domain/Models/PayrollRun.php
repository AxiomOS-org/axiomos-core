<?php
declare(strict_types=1);
namespace Modules\HR\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class PayrollRun extends Model {
    use HasUuids;
    protected $table = 'hr_payroll_runs';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'period_label',
  3 => 'status',
  4 => 'total_amount',
  5 => 'currency',
  6 => 'journal_id',
);
}