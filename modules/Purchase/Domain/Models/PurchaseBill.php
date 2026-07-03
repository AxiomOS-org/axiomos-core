<?php
declare(strict_types=1);
namespace Modules\Purchase\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class PurchaseBill extends Model {
    use HasUuids;
    protected $table = 'purchase_bills';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'vendor_id',
  3 => 'bill_number',
  4 => 'status',
  5 => 'total_amount',
  6 => 'currency',
  7 => 'journal_id',
);
}