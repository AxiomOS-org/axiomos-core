<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Illuminate\Support\Collection; interface JournalLineRepositoryInterface { public function create(array $attributes): void; public function byJournal(string $journalId): Collection; }

