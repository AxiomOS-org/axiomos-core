<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Support\Carbon;
use Modules\Identity\Domain\Models\IdentitySession;
use Modules\Users\Domain\Models\User;

final class SessionManagerService
{
    /**
     * @return array{session: IdentitySession, plain_token: string}
     */
    public function create(User $user, ?string $ipAddress = null, ?string $userAgent = null): array
    {
        $plain = bin2hex(random_bytes(32));
        $session = IdentitySession::query()->create([
            'identity_id' => $user->identity_id,
            'session_token_hash' => hash('sha256', $plain),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'started_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addDays(7),
            'status' => 'active',
        ]);

        return ['session' => $session, 'plain_token' => $plain];
    }

    public function revoke(string $sessionId): void
    {
        IdentitySession::query()->whereKey($sessionId)->update(['status' => 'revoked', 'expires_at' => Carbon::now()]);
    }

    /**
     * @return list<IdentitySession>
     */
    public function listByUser(User $user): array
    {
        return IdentitySession::query()
            ->where('identity_id', $user->identity_id)
            ->orderByDesc('created_at')
            ->get()
            ->all();
    }

    public function validateSession(string $sessionToken): ?IdentitySession
    {
        $hash = hash('sha256', $sessionToken);

        return IdentitySession::query()
            ->where('session_token_hash', $hash)
            ->where('status', 'active')
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })
            ->first();
    }
}
