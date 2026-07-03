<?php
declare(strict_types=1);
namespace Modules\Projects\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class ProjectTask extends Model {
    use HasUuids;
    protected $table = 'projects_tasks';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'project_id',
  3 => 'title',
  4 => 'status',
  5 => 'assignee_id',
);
}