<?php
declare(strict_types=1);
namespace Modules\Budgeting\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class BudgetLine extends Model {
    use HasUuids;
    protected $table = 'budget_lines';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'budget_version_id',
  3 => 'account_id',
  4 => 'period_label',
  5 => 'amount',
  6 => 'currency',
);
}