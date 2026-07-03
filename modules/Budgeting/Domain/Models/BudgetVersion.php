<?php
declare(strict_types=1);
namespace Modules\Budgeting\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class BudgetVersion extends Model {
    use HasUuids;
    protected $table = 'budget_versions';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'name',
  3 => 'fiscal_year',
  4 => 'status',
);
}