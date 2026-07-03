<?php
declare(strict_types=1);
namespace Modules\Reporting\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class ReportDefinition extends Model {
    use HasUuids;
    protected $table = 'reporting_definitions';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'code',
  3 => 'name',
  4 => 'report_type',
  5 => 'status',
);
}