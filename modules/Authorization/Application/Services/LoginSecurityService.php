<?php

declare(strict_types=1);

namespace Modules\Authorization\Application\Services;

final class LoginSecurityService
{
    /** @var array<string, array{count: int, first_at: int}> */
    private static array $attempts = [];

    public function sanitizeEmail(string $email): string
    {
        $sanitized = strtolower(trim($email));
        $sanitized = preg_replace('/[^a-z0-9@\.\+\_\-]/', '', $sanitized) ?? '';
        $sanitized = preg_replace('/\-{2,}/', '-', $sanitized) ?? '';

        return $sanitized;
    }

    public function registerFailedAttempt(string $identifier): void
    {
        $key = $this->sanitizeEmail($identifier);
        $now = time();
        $window = 60;

        if (! isset(self::$attempts[$key]) || ($now - self::$attempts[$key]['first_at']) > $window) {
            self::$attempts[$key] = ['count' => 1, 'first_at' => $now];
            return;
        }

        self::$attempts[$key]['count']++;
    }

    public function isRateLimited(string $identifier, int $maxAttempts = 5): bool
    {
        $key = $this->sanitizeEmail($identifier);
        $data = self::$attempts[$key] ?? null;
        if ($data === null) {
            return false;
        }

        return $data['count'] >= $maxAttempts;
    }

    public function reset(string $identifier): void
    {
        unset(self::$attempts[$this->sanitizeEmail($identifier)]);
    }
}
