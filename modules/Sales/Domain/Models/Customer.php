<?php
declare(strict_types=1);
namespace Modules\Sales\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class Customer extends Model {
    use HasUuids;
    protected $table = 'sales_customers';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'name',
  3 => 'email',
  4 => 'phone',
  5 => 'status',
);
}