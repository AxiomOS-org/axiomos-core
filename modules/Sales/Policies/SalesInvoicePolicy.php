<?php
declare(strict_types=1);
namespace Modules\Sales\Policies;
final class SalesInvoicePolicy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}