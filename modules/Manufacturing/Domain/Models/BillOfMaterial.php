<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class BillOfMaterial extends Model {
    use HasUuids;
    protected $table = 'manufacturing_boms';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'item_id',
  3 => 'version',
  4 => 'status',
);
}