<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\DTOs;

final readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?string $deviceFingerprint = null,
        public bool $rememberDevice = false,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: (string) $data['email'],
            password: (string) $data['password'],
            ipAddress: isset($data['ip_address']) ? (string) $data['ip_address'] : null,
            userAgent: isset($data['user_agent']) ? (string) $data['user_agent'] : null,
            deviceFingerprint: isset($data['device_fingerprint']) ? (string) $data['device_fingerprint'] : null,
            rememberDevice: (bool) ($data['remember_device'] ?? false),
        );
    }
}
