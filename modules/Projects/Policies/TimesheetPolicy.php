<?php
declare(strict_types=1);
namespace Modules\Projects\Policies;
final class TimesheetPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}