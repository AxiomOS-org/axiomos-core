<?php

declare(strict_types=1);

namespace Modules\Authentication\Infrastructure\Persistence;

use Modules\Authentication\Domain\Models\AuthCredential;
use Modules\Authentication\Domain\Repositories\Contracts\CredentialRepositoryInterface;

final class EloquentCredentialRepository implements CredentialRepositoryInterface
{
    public function findByUserId(string $userId): ?AuthCredential
    {
        return AuthCredential::query()->where('user_id', $userId)->first();
    }

    public function create(array $attributes): AuthCredential
    {
        return AuthCredential::query()->create($attributes);
    }

    public function update(AuthCredential $credential, array $attributes): AuthCredential
    {
        $credential->fill($attributes);
        $credential->save();

        return $credential->refresh();
    }
}
