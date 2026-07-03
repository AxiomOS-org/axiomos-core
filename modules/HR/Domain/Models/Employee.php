<?php
declare(strict_types=1);
namespace Modules\HR\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class Employee extends Model {
    use HasUuids;
    protected $table = 'hr_employees';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'employee_code',
  3 => 'full_name',
  4 => 'email',
  5 => 'status',
);
}