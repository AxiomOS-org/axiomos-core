<?php

declare(strict_types=1);

namespace Modules\Authentication\Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Modules\Authentication\Domain\Models\AuthCredential;
use Modules\Authentication\Domain\Models\AuthOauthClient;
use Modules\Authentication\Domain\Models\AuthPasswordPolicy;
use Modules\Users\Domain\Models\User;

final class AuthenticationDemoSeeder
{
    public function run(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('auth_credentials')) {
            return;
        }

        if (AuthPasswordPolicy::query()->count() === 0) {
            AuthPasswordPolicy::query()->create([
                'organization_id' => null,
                'rules' => [
                    'require_uppercase' => true,
                    'require_lowercase' => true,
                    'require_numeric' => true,
                    'require_symbol' => true,
                ],
                'min_length' => 12,
                'expiry_days' => 90,
                'history_count' => 5,
                'lockout_threshold' => 5,
                'lockout_minutes' => 15,
                'status' => 'active',
            ]);
        }

        $users = User::query()->orderBy('created_at')->limit(3)->get();
        foreach ($users as $user) {
            AuthCredential::query()->updateOrCreate(
                ['user_id' => (string) $user->id],
                [
                    'password_hash' => password_hash('AxiomOS@2026!', PASSWORD_ARGON2ID),
                    'email_verified_at' => Carbon::now(),
                    'failed_attempts' => 0,
                    'must_change_password' => false,
                    'password_changed_at' => Carbon::now(),
                    'status' => 'active',
                ],
            );
        }

        if (AuthOauthClient::query()->count() === 0) {
            AuthOauthClient::query()->create([
                'client_id' => 'axiomos-demo-client',
                'client_secret_hash' => password_hash('AxiomOS-Demo-Client-Secret', PASSWORD_ARGON2ID),
                'name' => 'AxiomOS Demo Client',
                'redirect_uris' => ['https://localhost/auth/callback'],
                'scopes' => ['auth:read', 'auth:write'],
                'status' => 'active',
            ]);
        }
    }
}
