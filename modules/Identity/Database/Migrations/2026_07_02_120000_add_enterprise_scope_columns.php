<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @param list<string> $columns
     */
    private function addScopeColumns(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $table): void {
            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    $blueprint->uuid($column)->nullable();
                }
            }
        });
    }

    public function up(): void
    {
        $this->addScopeColumns('identities', ['company_id', 'branch_id']);
        $this->addScopeColumns('teams', ['company_id', 'branch_id', 'department_id']);
        $this->addScopeColumns('team_members', ['organization_id', 'company_id', 'branch_id', 'department_id']);
        $this->addScopeColumns('contacts', ['organization_id', 'company_id', 'branch_id', 'department_id']);
        $this->addScopeColumns('devices', ['organization_id', 'company_id', 'branch_id', 'department_id']);
        $this->addScopeColumns('identity_sessions', ['organization_id', 'company_id', 'branch_id', 'department_id']);
        $this->addScopeColumns('api_tokens', ['organization_id', 'company_id', 'branch_id', 'department_id']);
        $this->addScopeColumns('login_history', ['organization_id', 'company_id', 'branch_id', 'department_id']);
        $this->addScopeColumns('users', ['organization_id', 'company_id', 'branch_id', 'department_id']);
        $this->addScopeColumns('memberships', ['company_id', 'branch_id', 'department_id']);
    }

    public function down(): void
    {
        foreach ([
            'identities' => ['company_id', 'branch_id'],
            'teams' => ['company_id', 'branch_id', 'department_id'],
            'team_members' => ['organization_id', 'company_id', 'branch_id', 'department_id'],
            'contacts' => ['organization_id', 'company_id', 'branch_id', 'department_id'],
            'devices' => ['organization_id', 'company_id', 'branch_id', 'department_id'],
            'identity_sessions' => ['organization_id', 'company_id', 'branch_id', 'department_id'],
            'api_tokens' => ['organization_id', 'company_id', 'branch_id', 'department_id'],
            'login_history' => ['organization_id', 'company_id', 'branch_id', 'department_id'],
            'users' => ['organization_id', 'company_id', 'branch_id', 'department_id'],
            'memberships' => ['company_id', 'branch_id', 'department_id'],
            'employee_profiles' => ['company_id', 'branch_id'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns): void {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $blueprint->dropColumn($column);
                    }
                }
            });
        }
    }
};
