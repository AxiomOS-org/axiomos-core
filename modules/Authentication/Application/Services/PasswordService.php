<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Support\Carbon;
use Modules\Authentication\Application\DTOs\PasswordChangeDTO;
use Modules\Authentication\Domain\Models\AuthPasswordHistory;
use Modules\Authentication\Domain\Repositories\Contracts\CredentialRepositoryInterface;
use Modules\Authentication\Domain\Repositories\Contracts\PasswordPolicyRepositoryInterface;
use RuntimeException;

final class PasswordService
{
    public function __construct(
        private readonly CredentialRepositoryInterface $credentials,
        private readonly PasswordPolicyRepositoryInterface $policies,
    ) {
    }

    public function change(PasswordChangeDTO $dto): void
    {
        $credential = $this->credentials->findByUserId($dto->userId)
            ?? throw new RuntimeException('Credentials not found for user.');

        if (! password_verify($dto->currentPassword, (string) $credential->password_hash)) {
            throw new RuntimeException('Current password is invalid.');
        }

        $policy = $this->policies->findByOrganization($dto->organizationId);
        $this->validatePolicy($dto->newPassword, $policy?->min_length ?? 12);
        $this->assertNotInHistory($dto->userId, $dto->newPassword, $policy?->history_count ?? 5);

        AuthPasswordHistory::query()->create([
            'user_id' => $dto->userId,
            'password_hash' => (string) $credential->password_hash,
            'status' => 'archived',
        ]);

        $this->credentials->update($credential, [
            'password_hash' => password_hash($dto->newPassword, PASSWORD_ARGON2ID),
            'password_changed_at' => Carbon::now(),
            'must_change_password' => false,
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function validatePolicy(string $password, int $minLength): void
    {
        if (mb_strlen($password) < $minLength) {
            throw new RuntimeException(sprintf('Password must be at least %d characters.', $minLength));
        }

        if (! preg_match('/[A-Z]/', $password) || ! preg_match('/[a-z]/', $password) || ! preg_match('/\d/', $password) || ! preg_match('/[^A-Za-z0-9]/', $password)) {
            throw new RuntimeException('Password must contain upper, lower, number and symbol.');
        }
    }

    public function assertNotInHistory(string $userId, string $newPassword, int $historyCount): void
    {
        $history = AuthPasswordHistory::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(max(1, $historyCount))
            ->get();

        foreach ($history as $entry) {
            if (password_verify($newPassword, (string) $entry->password_hash)) {
                throw new RuntimeException('Password has been used previously.');
            }
        }
    }
}
