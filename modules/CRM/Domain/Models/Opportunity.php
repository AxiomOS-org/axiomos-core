<?php
declare(strict_types=1);
namespace Modules\CRM\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class Opportunity extends Model {
    use HasUuids;
    protected $table = 'crm_opportunities';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'lead_id',
  3 => 'title',
  4 => 'stage',
  5 => 'amount',
  6 => 'currency',
);
}