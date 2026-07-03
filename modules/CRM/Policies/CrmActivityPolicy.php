<?php
declare(strict_types=1);
namespace Modules\CRM\Policies;
final class CrmActivityPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}