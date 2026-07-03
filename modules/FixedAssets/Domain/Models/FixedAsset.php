<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class FixedAsset extends Model {
    use HasUuids;
    protected $table = 'fixed_assets';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'asset_code',
  3 => 'name',
  4 => 'status',
  5 => 'acquisition_cost',
  6 => 'currency',
);
}