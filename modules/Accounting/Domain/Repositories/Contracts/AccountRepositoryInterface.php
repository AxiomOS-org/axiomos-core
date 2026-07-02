<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\Account; interface AccountRepositoryInterface { public function create(array $attributes): Account; public function find(string $id): ?Account; public function byCompany(string $companyId): Collection; }

