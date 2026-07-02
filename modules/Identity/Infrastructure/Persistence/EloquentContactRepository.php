<?php

declare(strict_types=1);

namespace Modules\Identity\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\Contact;
use Modules\Identity\Domain\Repositories\Contracts\ContactRepositoryInterface;
use Modules\Identity\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentContactRepository implements ContactRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $identityId = null): Collection
    {
        $query = Contact::query()->orderByDesc('is_primary')->orderBy('contact_type');

        if ($identityId !== null) {
            $query->where('identity_id', $identityId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = Contact::query();

        if ($query->identityId !== null) {
            $builder->where('identity_id', $query->identityId);
        }

        $this->applyListQuery($builder, $query, ['contact_type', 'value']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?Contact
    {
        return Contact::query()->find($id);
    }

    public function create(array $attributes): Contact
    {
        return Contact::query()->create($attributes);
    }

    public function update(Contact $contact, array $attributes): Contact
    {
        $contact->fill($attributes);
        $contact->save();

        return $contact->refresh();
    }

    public function delete(Contact $contact): void
    {
        $contact->delete();
    }
}
