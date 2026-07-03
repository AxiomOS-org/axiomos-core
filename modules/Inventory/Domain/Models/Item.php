<?php
declare(strict_types=1);
namespace Modules\Inventory\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class Item extends Model {
    use HasUuids;
    protected $table = 'inventory_items';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'sku',
  3 => 'name',
  4 => 'unit',
  5 => 'status',
);
}