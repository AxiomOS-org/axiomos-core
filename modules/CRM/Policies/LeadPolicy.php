<?php
declare(strict_types=1);
namespace Modules\CRM\Policies;
final class LeadPolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}