<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Support\Carbon;
use Modules\Authentication\Domain\Models\AuthPasswordReset;
use Modules\Authentication\Domain\Repositories\Contracts\CredentialRepositoryInterface;
use Modules\Users\Domain\Models\User;
use RuntimeException;

final class PasswordResetService
{
    public function __construct(
        private readonly CredentialRepositoryInterface $credentials,
        private readonly PasswordService $passwords,
    ) {
    }

    public function forgot(string $email): ?string
    {
        $user = User::query()->where('email', $email)->first();
        if ($user === null) {
            return null;
        }

        $token = bin2hex(random_bytes(24));
        AuthPasswordReset::query()->create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => Carbon::now()->addMinutes(30),
            'status' => 'pending',
        ]);

        return $token;
    }

    public function reset(string $token, string $newPassword): void
    {
        $record = AuthPasswordReset::query()
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('used_at')
            ->orderByDesc('created_at')
            ->first();

        if ($record === null || ($record->expires_at !== null && $record->expires_at->isPast())) {
            throw new RuntimeException('Password reset token is invalid or expired.');
        }

        $this->passwords->validatePolicy($newPassword, 12);
        $this->passwords->assertNotInHistory((string) $record->user_id, $newPassword, 5);

        $credential = $this->credentials->findByUserId((string) $record->user_id)
            ?? throw new RuntimeException('Credentials not found for user.');

        $this->credentials->update($credential, [
            'password_hash' => password_hash($newPassword, PASSWORD_ARGON2ID),
            'password_changed_at' => Carbon::now(),
            'must_change_password' => false,
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        $record->update(['used_at' => Carbon::now(), 'status' => 'used']);
    }
}
