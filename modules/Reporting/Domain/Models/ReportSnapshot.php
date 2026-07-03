<?php
declare(strict_types=1);
namespace Modules\Reporting\Domain\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class ReportSnapshot extends Model {
    use HasUuids;
    protected $table = 'reporting_snapshots';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = array (
  0 => 'organization_id',
  1 => 'company_id',
  2 => 'report_definition_id',
  3 => 'snapshot_date',
  4 => 'status',
  5 => 'payload_json',
);
}