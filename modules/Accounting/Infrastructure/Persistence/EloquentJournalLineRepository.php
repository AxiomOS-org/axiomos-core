<?php
declare(strict_types=1);
namespace Modules\Accounting\Infrastructure\Persistence;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\JournalLine; use Modules\Accounting\Domain\Repositories\Contracts\JournalLineRepositoryInterface; final class EloquentJournalLineRepository implements JournalLineRepositoryInterface { public function create(array $attributes): void { JournalLine::query()->create($attributes); } public function byJournal(string $journalId): Collection { return JournalLine::query()->where('journal_id',$journalId)->get()->map(static fn($x): array => $x->toArray()); } }

