<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\Journal; interface JournalRepositoryInterface { public function create(array $attributes): Journal; public function find(string $id): ?Journal; public function byCompany(string $companyId): Collection; }

