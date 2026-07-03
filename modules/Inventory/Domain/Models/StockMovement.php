<?php
declare(strict_types=1);
namespace Modules\Inventory\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class StockMovement extends Model {
    use HasUuids;
    protected $table = 'inventory_stock_movements';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'warehouse_id',
  3 => 'item_id',
  4 => 'movement_type',
  5 => 'quantity',
  6 => 'reference',
);
}