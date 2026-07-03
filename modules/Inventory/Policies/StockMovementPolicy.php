<?php
declare(strict_types=1);
namespace Modules\Inventory\Policies;
final class StockMovementPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}