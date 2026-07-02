<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Support\Carbon;
use Modules\Authentication\Application\DTOs\LoginDTO;
use Modules\Authentication\Domain\Repositories\Contracts\CredentialRepositoryInterface;
use Modules\Users\Domain\Models\User;
use RuntimeException;

final class AuthenticationService
{
    public function __construct(
        private readonly CredentialRepositoryInterface $credentials,
        private readonly RateLimitService $rateLimits,
        private readonly SessionManagerService $sessions,
        private readonly LoginHistoryService $loginHistory,
        private readonly TrustedDeviceService $trustedDevices,
        private readonly SecurityEventService $securityEvents,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function login(LoginDTO $dto): array
    {
        $rateKey = 'login:' . strtolower($dto->email);
        if ($this->rateLimits->tooManyAttempts($rateKey)) {
            $this->securityEvents->record('auth.locked', null, $dto->ipAddress, $dto->userAgent, ['email' => $dto->email], 'high');
            throw new RuntimeException('Too many attempts. Account temporarily locked.');
        }

        $user = User::query()->where('email', $dto->email)->first();
        if ($user === null) {
            $this->rateLimits->hit($rateKey);
            throw new RuntimeException('Invalid credentials.');
        }

        $credential = $this->credentials->findByUserId((string) $user->id);
        if ($credential === null || ! password_verify($dto->password, (string) $credential->password_hash)) {
            $this->rateLimits->hit($rateKey);
            $this->loginHistory->record($user, false, $dto->ipAddress, $dto->userAgent);

            if ($credential !== null) {
                $failed = ((int) $credential->failed_attempts) + 1;
                $updates = ['failed_attempts' => $failed];
                if ($failed >= 5) {
                    $updates['locked_until'] = Carbon::now()->addMinutes(15);
                }
                $this->credentials->update($credential, $updates);
            }

            throw new RuntimeException('Invalid credentials.');
        }

        if ($credential->locked_until !== null && $credential->locked_until->isFuture()) {
            throw new RuntimeException('Account is locked.');
        }

        $this->rateLimits->clear($rateKey);
        $this->credentials->update($credential, ['failed_attempts' => 0, 'locked_until' => null]);
        $this->loginHistory->record($user, true, $dto->ipAddress, $dto->userAgent);

        $session = $this->sessions->create($user, $dto->ipAddress, $dto->userAgent);

        if ($dto->rememberDevice && $dto->deviceFingerprint !== null) {
            $this->trustedDevices->remember((string) $user->id, $dto->deviceFingerprint, $dto->ipAddress, $dto->userAgent);
        }

        $this->securityEvents->record('auth.login.success', (string) $user->id, $dto->ipAddress, $dto->userAgent, ['email' => $dto->email]);

        return [
            'user' => [
                'id' => $user->id,
                'identity_id' => $user->identity_id,
                'email' => $user->email,
                'username' => $user->username,
                'display_name' => $user->display_name,
            ],
            'session' => [
                'id' => $session['session']->id,
                'token' => $session['plain_token'],
                'expires_at' => $session['session']->expires_at?->toIso8601String(),
            ],
            'must_change_password' => (bool) $credential->must_change_password,
            'email_verified' => $credential->email_verified_at !== null,
        ];
    }

    public function logout(string $sessionId): void
    {
        $this->sessions->revoke($sessionId);
    }

    public function validateSession(string $token): bool
    {
        return $this->sessions->validateSession($token) !== null;
    }
}
