<?php
declare(strict_types=1);
namespace Modules\Inventory\Policies;
final class WarehousePolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}