<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Services;

use Modules\Accounting\Domain\Models\Document;
use Modules\Accounting\Domain\Repositories\Contracts\DocumentRepositoryInterface;

final class DocumentService
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documents,
        private readonly AccountingPlatformHooks $hooks,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function list(string $companyId): array
    {
        return $this->documents->byCompany($companyId)
            ->map(static fn (Document $document): array => $document->toArray())
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $id): ?array
    {
        return $this->documents->find($id)?->toArray();
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    public function create(array $attributes): array
    {
        $attributes['status'] = $attributes['status'] ?? Document::STATUS_DRAFT;
        $document = $this->documents->create($attributes);
        $this->hooks->onCreated($document);

        return $document->toArray();
    }
}
