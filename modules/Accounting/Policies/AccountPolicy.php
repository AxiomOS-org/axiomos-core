<?php
declare(strict_types=1);
namespace Modules\Accounting\Policies;
final class AccountPolicy { public function viewAny(): bool { return true; } public function view(): bool { return true; } public function create(): bool { return true; } public function update(): bool { return true; } public function delete(): bool { return true; } }

