<?php
declare(strict_types=1);
namespace Modules\Budgeting\Policies;
final class BudgetLinePolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}