<?php
declare(strict_types=1);
namespace Modules\HR\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class AttendanceRecord extends Model {
    use HasUuids;
    protected $table = 'hr_attendance_records';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'employee_id',
  3 => 'work_date',
  4 => 'status',
  5 => 'hours_worked',
);
}