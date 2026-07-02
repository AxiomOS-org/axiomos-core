<?php
declare(strict_types=1);
namespace Modules\Accounting\Infrastructure\Persistence;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\Account; use Modules\Accounting\Domain\Repositories\Contracts\AccountRepositoryInterface; final class EloquentAccountRepository implements AccountRepositoryInterface { public function create(array $attributes): Account { return Account::query()->create($attributes); } public function find(string $id): ?Account { return Account::query()->find($id); } public function byCompany(string $companyId): Collection { return Account::query()->where('company_id',$companyId)->orderBy('account_code')->get(); } }

