<?php
declare(strict_types=1);
namespace Modules\Purchase\Policies;
final class PurchaseOrderPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}