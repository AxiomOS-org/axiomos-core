<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Policies;
final class BillOfMaterialPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}