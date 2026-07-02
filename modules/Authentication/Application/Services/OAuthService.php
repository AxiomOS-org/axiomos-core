<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Support\Carbon;
use Modules\Authentication\Application\DTOs\LoginDTO;
use Modules\Authentication\Application\DTOs\OAuthTokenRequestDTO;
use Modules\Authentication\Domain\Models\AuthOauthClient;
use Modules\Authentication\Domain\Models\AuthOauthToken;
use Modules\Identity\Domain\Models\ApiToken;
use RuntimeException;

final class OAuthService
{
    public function __construct(private readonly AuthenticationService $auth)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function issueToken(OAuthTokenRequestDTO $dto): array
    {
        if ($dto->grantType === 'client_credentials') {
            return $this->clientCredentials($dto);
        }

        if ($dto->grantType === 'password') {
            return $this->passwordGrant($dto);
        }

        return [
            'grant_type' => 'authorization_code',
            'status' => 'stub',
            'message' => 'Authorization code grant is stubbed for Phase 5.C.',
        ];
    }

    /**
     * @param list<string> $scopes
     *
     * @return array{token_id: string, plain_text_token: string}
     */
    public function issuePersonalAccessToken(string $identityId, string $name, array $scopes): array
    {
        $plain = bin2hex(random_bytes(32));
        $token = ApiToken::query()->create([
            'identity_id' => $identityId,
            'name' => $name,
            'token_hash' => hash('sha256', $plain),
            'scopes' => $scopes,
            'expires_at' => Carbon::now()->addDays(90),
            'status' => 'active',
        ]);

        return ['token_id' => (string) $token->id, 'plain_text_token' => $plain];
    }

    /**
     * @return array<string, mixed>
     */
    private function clientCredentials(OAuthTokenRequestDTO $dto): array
    {
        $client = AuthOauthClient::query()->where('client_id', $dto->clientId)->whereNull('revoked_at')->first();
        if ($client === null || $dto->clientSecret === null || ! password_verify($dto->clientSecret, (string) $client->client_secret_hash)) {
            throw new RuntimeException('Invalid OAuth client credentials.');
        }

        $accessToken = bin2hex(random_bytes(32));
        $refreshToken = bin2hex(random_bytes(32));
        AuthOauthToken::query()->create([
            'oauth_client_id' => $client->id,
            'user_id' => null,
            'access_token_hash' => hash('sha256', $accessToken),
            'refresh_token_hash' => hash('sha256', $refreshToken),
            'scopes' => $dto->scopes,
            'expires_at' => Carbon::now()->addHour(),
            'status' => 'active',
        ]);

        return [
            'token_type' => 'Bearer',
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 3600,
            'scope' => implode(' ', $dto->scopes),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function passwordGrant(OAuthTokenRequestDTO $dto): array
    {
        if ($dto->email === null || $dto->password === null) {
            throw new RuntimeException('Email and password are required for password grant.');
        }

        $result = $this->auth->login(new LoginDTO($dto->email, $dto->password));

        $accessToken = bin2hex(random_bytes(32));
        $refreshToken = bin2hex(random_bytes(32));
        AuthOauthToken::query()->create([
            'oauth_client_id' => null,
            'user_id' => $result['user']['id'],
            'access_token_hash' => hash('sha256', $accessToken),
            'refresh_token_hash' => hash('sha256', $refreshToken),
            'scopes' => $dto->scopes,
            'expires_at' => Carbon::now()->addHour(),
            'status' => 'active',
        ]);

        return [
            'token_type' => 'Bearer',
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 3600,
            'scope' => implode(' ', $dto->scopes),
            'session' => $result['session'],
        ];
    }
}
