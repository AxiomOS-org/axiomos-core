<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Policies;
final class DepreciationRunPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}