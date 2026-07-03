<?php
declare(strict_types=1);
namespace Modules\Projects\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class Timesheet extends Model {
    use HasUuids;
    protected $table = 'projects_timesheets';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'project_id',
  3 => 'employee_id',
  4 => 'work_date',
  5 => 'hours',
  6 => 'status',
);
}