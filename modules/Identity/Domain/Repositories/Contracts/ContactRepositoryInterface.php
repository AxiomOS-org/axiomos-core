<?php

declare(strict_types=1);

namespace Modules\Identity\Domain\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Contact;

interface ContactRepositoryInterface
{
    /** @return Collection<int, Contact> */
    public function all(?string $identityId = null): Collection;

    public function paginate(ListQuery $query): LengthAwarePaginator;

    public function find(string $id): ?Contact;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Contact;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Contact $contact, array $attributes): Contact;

    public function delete(Contact $contact): void;
}
