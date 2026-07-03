<?php
declare(strict_types=1);
namespace Modules\Sales\Policies;
final class SalesOrderPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}