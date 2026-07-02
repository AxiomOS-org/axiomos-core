<?php

declare(strict_types=1);

namespace Modules\Membership\Database\Seeders;

use Modules\Membership\Domain\Models\Membership;
use Modules\Organization\Domain\Models\Organization;
use Modules\Users\Domain\Models\User;

final class MembershipDemoSeeder
{
    public function run(): void
    {
        if (Membership::query()->count() > 0) {
            return;
        }

        $users = User::query()->orderBy('created_at')->limit(20)->get();
        $organizations = Organization::query()->orderBy('created_at')->limit(5)->get();

        if ($users->isEmpty() || $organizations->isEmpty()) {
            return;
        }

        foreach ($users as $index => $user) {
            $organization = $organizations[$index % $organizations->count()];
            $type = $index === 0 ? 'owner' : ($index < 4 ? 'admin' : 'member');

            Membership::query()->create([
                'user_id' => (string) $user->id,
                'organization_id' => (string) $organization->id,
                'membership_type' => $type,
                'status' => 'active',
                'scopes' => ['read', 'write'],
            ]);
        }
    }
}
