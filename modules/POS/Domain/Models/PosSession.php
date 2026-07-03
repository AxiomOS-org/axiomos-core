<?php
declare(strict_types=1);
namespace Modules\POS\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class PosSession extends Model {
    use HasUuids;
    protected $table = 'pos_sessions';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'terminal_id',
  3 => 'opened_at',
  4 => 'closed_at',
  5 => 'status',
);
}