<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class WorkOrder extends Model {
    use HasUuids;
    protected $table = 'manufacturing_work_orders';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'bom_id',
  3 => 'order_number',
  4 => 'status',
  5 => 'quantity',
);
}