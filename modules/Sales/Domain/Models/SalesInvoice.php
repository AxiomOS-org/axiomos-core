<?php
declare(strict_types=1);
namespace Modules\Sales\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class SalesInvoice extends Model {
    use HasUuids;
    protected $table = 'sales_invoices';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'customer_id',
  3 => 'invoice_number',
  4 => 'status',
  5 => 'total_amount',
  6 => 'currency',
  7 => 'journal_id',
);
}