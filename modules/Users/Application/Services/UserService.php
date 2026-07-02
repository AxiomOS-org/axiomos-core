<?php

declare(strict_types=1);

namespace Modules\Users\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Users\Application\DTOs\CreateUserDTO;
use Modules\Users\Application\DTOs\UpdateUserDTO;
use Modules\Users\Application\Support\ListQuery;
use Modules\Users\Domain\Models\User;
use Modules\Users\Domain\Repositories\Contracts\UserRepositoryInterface;
use RuntimeException;

final class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly UsersPlatformHooks $platform,
    ) {
    }

    /** @return Collection<int, User> */
    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query);
    }

    public function get(string $id): User
    {
        return $this->repository->find($id)
            ?? throw new RuntimeException(sprintf('User "%s" not found.', $id));
    }

    public function create(CreateUserDTO $dto): User
    {
        $user = $this->repository->create($dto->toAttributes());
        $this->platform->onCreated($user);

        return $user;
    }

    public function update(string $id, UpdateUserDTO $dto): User
    {
        $user = $this->get($id);
        $before = $user->toAuditSnapshot();
        $updated = $this->repository->update($user, $dto->toAttributes());
        $this->platform->onUpdated($updated, $before);

        return $updated;
    }

    public function delete(string $id): void
    {
        $user = $this->get($id);
        $this->platform->onDeleted($user);
        $this->repository->delete($user);
    }
}
