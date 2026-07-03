<?php
declare(strict_types=1);
namespace Modules\Sales\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class SalesOrder extends Model {
    use HasUuids;
    protected $table = 'sales_orders';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'customer_id',
  3 => 'order_number',
  4 => 'status',
  5 => 'total_amount',
  6 => 'currency',
);
}