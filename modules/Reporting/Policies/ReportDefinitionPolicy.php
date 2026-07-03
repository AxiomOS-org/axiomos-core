<?php
declare(strict_types=1);
namespace Modules\Reporting\Policies;
final class ReportDefinitionPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}