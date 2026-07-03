<?php
declare(strict_types=1);
namespace Modules\POS\Policies;
final class PosOrderPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}