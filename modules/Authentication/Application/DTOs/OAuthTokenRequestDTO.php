<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\DTOs;

final readonly class OAuthTokenRequestDTO
{
    /**
     * @param list<string> $scopes
     */
    public function __construct(
        public string $grantType,
        public ?string $clientId = null,
        public ?string $clientSecret = null,
        public ?string $email = null,
        public ?string $password = null,
        public array $scopes = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $scope = isset($data['scope']) && is_string($data['scope']) ? trim($data['scope']) : '';

        return new self(
            grantType: (string) $data['grant_type'],
            clientId: isset($data['client_id']) ? (string) $data['client_id'] : null,
            clientSecret: isset($data['client_secret']) ? (string) $data['client_secret'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            password: isset($data['password']) ? (string) $data['password'] : null,
            scopes: $scope === '' ? [] : (preg_split('/\s+/', $scope) ?: []),
        );
    }
}
