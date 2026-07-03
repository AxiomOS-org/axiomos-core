<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class ProductionRun extends Model {
    use HasUuids;
    protected $table = 'manufacturing_production_runs';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'work_order_id',
  3 => 'status',
  4 => 'quantity_produced',
  5 => 'journal_id',
);
}