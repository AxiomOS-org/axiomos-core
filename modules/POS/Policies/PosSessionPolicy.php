<?php
declare(strict_types=1);
namespace Modules\POS\Policies;
final class PosSessionPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}