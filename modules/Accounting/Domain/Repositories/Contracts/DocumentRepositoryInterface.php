<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\Document; interface DocumentRepositoryInterface { public function create(array $attributes): Document; public function update(Document $document, array $attributes): Document; public function find(string $id): ?Document; public function findBySource(string $module, string $type, string $sourceId): ?Document; public function byCompany(string $companyId): Collection; }

