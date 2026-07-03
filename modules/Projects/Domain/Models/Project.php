<?php
declare(strict_types=1);
namespace Modules\Projects\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class Project extends Model {
    use HasUuids;
    protected $table = 'projects_projects';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'code',
  3 => 'name',
  4 => 'status',
  5 => 'budget_amount',
);
}