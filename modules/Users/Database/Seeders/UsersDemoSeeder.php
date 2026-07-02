<?php

declare(strict_types=1);

namespace Modules\Users\Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Users\Domain\Models\User;

final class UsersDemoSeeder
{
    public function run(): void
    {
        if (! Schema::hasTable('identities') || User::query()->count() > 0) {
            return;
        }

        $identities = DB::table('identities')
            ->select(['id'])
            ->orderBy('id')
            ->limit(10)
            ->get();

        foreach ($identities as $index => $identity) {
            $n = $index + 1;
            User::query()->create([
                'identity_id' => (string) $identity->id,
                'username' => sprintf('demo.user.%d', $n),
                'email' => sprintf('demo.user.%d@axiomos.local', $n),
                'display_name' => sprintf('Demo User %d', $n),
                'status' => 'active',
                'settings' => ['theme' => 'default'],
            ]);
        }
    }
}
