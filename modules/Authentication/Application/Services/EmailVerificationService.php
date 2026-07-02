<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Support\Carbon;
use Modules\Authentication\Domain\Models\AuthEmailVerification;
use Modules\Authentication\Domain\Repositories\Contracts\CredentialRepositoryInterface;
use Modules\Users\Domain\Models\User;
use RuntimeException;

final class EmailVerificationService
{
    public function __construct(private readonly CredentialRepositoryInterface $credentials)
    {
    }

    public function sendToken(string $email): ?string
    {
        $user = User::query()->where('email', $email)->first();
        if ($user === null) {
            return null;
        }

        $token = bin2hex(random_bytes(24));
        AuthEmailVerification::query()->create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);

        return $token;
    }

    public function verify(string $token): void
    {
        $record = AuthEmailVerification::query()
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('verified_at')
            ->orderByDesc('created_at')
            ->first();

        if ($record === null || ($record->expires_at !== null && $record->expires_at->isPast())) {
            throw new RuntimeException('Email verification token is invalid or expired.');
        }

        $credential = $this->credentials->findByUserId((string) $record->user_id)
            ?? throw new RuntimeException('Credentials not found for user.');

        $this->credentials->update($credential, ['email_verified_at' => Carbon::now()]);
        $record->update(['verified_at' => Carbon::now(), 'status' => 'verified']);
    }
}
