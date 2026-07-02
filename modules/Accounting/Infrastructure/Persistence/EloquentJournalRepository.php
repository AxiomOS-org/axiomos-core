<?php
declare(strict_types=1);
namespace Modules\Accounting\Infrastructure\Persistence;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\Journal; use Modules\Accounting\Domain\Repositories\Contracts\JournalRepositoryInterface; final class EloquentJournalRepository implements JournalRepositoryInterface { public function create(array $attributes): Journal { return Journal::query()->create($attributes); } public function find(string $id): ?Journal { return Journal::query()->find($id); } public function byCompany(string $companyId): Collection { return Journal::query()->where('company_id',$companyId)->latest('posting_date')->get(); } }

