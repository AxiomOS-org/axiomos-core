<?php

declare(strict_types=1);

namespace Modules\Identity\Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Identity\Domain\Models\ApiToken;
use Modules\Identity\Domain\Models\Contact;
use Modules\Identity\Domain\Models\Device;
use Modules\Identity\Domain\Models\EmployeeProfile;
use Modules\Identity\Domain\Models\Identity;
use Modules\Identity\Domain\Models\IdentitySession;
use Modules\Identity\Domain\Models\LoginHistory;
use Modules\Identity\Domain\Models\Team;
use Modules\Identity\Domain\Models\TeamMember;

final class IdentityDemoSeeder
{
    public function run(): void
    {
        if (! Schema::hasTable('organizations') || Identity::query()->count() > 0) {
            return;
        }

        $organizationIds = DB::table('organizations')
            ->select(['id'])
            ->orderBy('id')
            ->limit(10)
            ->pluck('id')
            ->map(static fn ($id): string => (string) $id)
            ->all();

        if ($organizationIds === []) {
            return;
        }

        $departmentByOrganization = $this->departmentMapByOrganization();

        foreach ($organizationIds as $index => $organizationId) {
            $n = $index + 1;
            $identity = Identity::query()->create([
                'organization_id' => $organizationId,
                'identity_type' => 'employee',
                'code' => sprintf('ID-%04d', $n),
                'display_name' => sprintf('Identity %02d', $n),
                'legal_name' => sprintf('Identity %02d Legal Name', $n),
                'email' => sprintf('identity.%02d@axiomos.local', $n),
                'phone' => sprintf('+1202555%04d', 1000 + $n),
                'status' => 'active',
                'metadata' => ['seed' => 'identity-demo', 'batch' => 'phase-5b'],
            ]);

            $team = Team::query()->create([
                'organization_id' => $organizationId,
                'code' => sprintf('TEAM-%02d', $n),
                'name' => sprintf('Identity Team %02d', $n),
                'description' => 'Auto-seeded identity team',
                'leader_identity_id' => $identity->id,
                'status' => 'active',
            ]);

            TeamMember::query()->create([
                'team_id' => $team->id,
                'identity_id' => $identity->id,
                'role' => 'lead',
            ]);

            EmployeeProfile::query()->create([
                'identity_id' => $identity->id,
                'organization_id' => $organizationId,
                'employee_number' => sprintf('EMP-%04d', $n),
                'job_title' => 'Operations Specialist',
                'department_id' => $departmentByOrganization[$organizationId] ?? null,
                'hire_date' => Carbon::now()->subDays(30 + $n)->toDateString(),
                'status' => 'active',
                'metadata' => ['source' => 'IdentityDemoSeeder'],
            ]);

            Contact::query()->create([
                'identity_id' => $identity->id,
                'contact_type' => 'email',
                'value' => sprintf('identity.%02d@axiomos.local', $n),
                'is_primary' => true,
                'status' => 'active',
            ]);

            Device::query()->create([
                'identity_id' => $identity->id,
                'device_type' => 'workstation',
                'fingerprint' => hash('sha256', sprintf('device-%d-%s', $n, $identity->id)),
                'user_agent' => sprintf('AxiomOS Seeder Agent/%d', $n),
                'last_seen_at' => Carbon::now()->subHours($n),
                'status' => 'active',
            ]);

            IdentitySession::query()->create([
                'identity_id' => $identity->id,
                'session_token_hash' => hash('sha256', sprintf('session-%d-%s', $n, $identity->id)),
                'ip_address' => sprintf('10.20.%d.%d', max(1, $n), 10 + $n),
                'user_agent' => 'AxiomOS Identity Seeder Session',
                'started_at' => Carbon::now()->subHours(4 + $n),
                'expires_at' => Carbon::now()->addHours(24),
                'status' => 'active',
            ]);

            if (Schema::hasTable('login_history')) {
                LoginHistory::query()->create([
                    'identity_id' => $identity->id,
                    'user_id' => null,
                    'ip_address' => sprintf('10.20.%d.%d', max(1, $n), 50 + $n),
                    'user_agent' => 'AxiomOS Identity Seeder Login',
                    'success' => true,
                    'logged_at' => Carbon::now()->subMinutes(15 * $n),
                    'status' => 'recorded',
                ]);
            }

            ApiToken::query()->create([
                'identity_id' => $identity->id,
                'name' => sprintf('demo-token-%02d', $n),
                'token_hash' => hash('sha256', sprintf('api-token-%d-%s', $n, $identity->id)),
                'scopes' => ['identity:read', 'identity:write'],
                'expires_at' => Carbon::now()->addDays(90),
                'last_used_at' => Carbon::now()->subMinutes($n),
                'status' => 'active',
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    private function departmentMapByOrganization(): array
    {
        if (! Schema::hasTable('departments') || ! Schema::hasTable('branches') || ! Schema::hasTable('companies')) {
            return [];
        }

        $map = [];

        $rows = DB::table('departments')
            ->join('branches', 'departments.branch_id', '=', 'branches.id')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->select(['companies.organization_id', 'departments.id as department_id'])
            ->orderBy('companies.organization_id')
            ->orderBy('departments.id')
            ->get();

        foreach ($rows as $row) {
            $organizationId = (string) $row->organization_id;

            if (! isset($map[$organizationId])) {
                $map[$organizationId] = (string) $row->department_id;
            }
        }

        return $map;
    }
}
