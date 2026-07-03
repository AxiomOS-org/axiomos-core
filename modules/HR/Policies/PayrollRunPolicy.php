<?php
declare(strict_types=1);
namespace Modules\HR\Policies;
final class PayrollRunPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}