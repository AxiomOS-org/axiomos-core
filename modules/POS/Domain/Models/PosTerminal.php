<?php
declare(strict_types=1);
namespace Modules\POS\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class PosTerminal extends Model {
    use HasUuids;
    protected $table = 'pos_terminals';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'code',
  3 => 'name',
  4 => 'status',
);
}