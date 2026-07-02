<?php

declare(strict_types=1);

namespace Modules\Authentication\Domain\Repositories\Contracts;

use Modules\Authentication\Domain\Models\AuthCredential;

interface CredentialRepositoryInterface
{
    public function findByUserId(string $userId): ?AuthCredential;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): AuthCredential;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(AuthCredential $credential, array $attributes): AuthCredential;
}
