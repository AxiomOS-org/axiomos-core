<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Contact;
use Modules\Identity\Domain\Repositories\Contracts\ContactRepositoryInterface;
use RuntimeException;

final class ContactService
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository,
        private readonly IdentityPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, Contact> */
    public function list(?string $identityId = null): Collection
    {
        return $this->repository->all($identityId);
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): Contact
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('Contact "%s" not found.', $id));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Contact
    {
        $contact = $this->repository->create($attributes);
        $this->platform->onCreated($contact);

        return $contact;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(string $id, array $attributes): Contact
    {
        $contact = $this->get($id);
        $before = $contact->toAuditSnapshot();
        $updated = $this->repository->update($contact, $attributes);
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $contact = $this->get($id);
        $this->platform->onDeleted($contact);
        $this->repository->delete($contact);
    }
}
