<?php
declare(strict_types=1);
namespace Modules\CRM\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class CrmActivity extends Model {
    use HasUuids;
    protected $table = 'crm_activities';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'subject',
  3 => 'activity_type',
  4 => 'status',
  5 => 'due_at',
);
}