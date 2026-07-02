<?php

declare(strict_types=1);

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Authorization\Domain\Models\AuthorizationPermission;
use Modules\Authorization\Domain\Models\AuthorizationRole;
use Modules\Authorization\Domain\Models\AuthorizationRoleAssignment;
use Modules\Authorization\Domain\Models\AuthorizationRolePermission;

final class AuthorizationDemoSeeder
{
    public function run(): void
    {
        if (
            ! Schema::hasTable('authorization_roles')
            || ! Schema::hasTable('authorization_permissions')
            || ! Schema::hasTable('authorization_role_assignments')
        ) {
            return;
        }

        $roles = [
            ['slug' => 'super-admin', 'name' => 'Super Admin', 'description' => 'Global unrestricted access', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'admin', 'name' => 'Admin', 'description' => 'Administrative security access', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'member', 'name' => 'Member', 'description' => 'Standard user role', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'guest', 'name' => 'Guest', 'description' => 'Restricted read-only role', 'is_system' => true, 'status' => 'active'],
        ];

        $permissions = [
            ['slug' => 'users.view', 'name' => 'View users', 'module' => 'users', 'action' => 'view', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'users.manage', 'name' => 'Manage users', 'module' => 'users', 'action' => 'manage', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'memberships.view', 'name' => 'View memberships', 'module' => 'memberships', 'action' => 'view', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'memberships.manage', 'name' => 'Manage memberships', 'module' => 'memberships', 'action' => 'manage', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'organizations.view', 'name' => 'View organizations', 'module' => 'organizations', 'action' => 'view', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'organizations.manage', 'name' => 'Manage organizations', 'module' => 'organizations', 'action' => 'manage', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'security.roles.view', 'name' => 'View roles', 'module' => 'security', 'action' => 'roles.view', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'security.roles.create', 'name' => 'Create roles', 'module' => 'security', 'action' => 'roles.create', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'security.roles.update', 'name' => 'Update roles', 'module' => 'security', 'action' => 'roles.update', 'is_system' => true, 'status' => 'active'],
            ['slug' => 'security.roles.delete', 'name' => 'Delete roles', 'module' => 'security', 'action' => 'roles.delete', 'is_system' => true, 'status' => 'active'],
        ];

        foreach ($roles as $attributes) {
            AuthorizationRole::query()->firstOrCreate(['slug' => $attributes['slug']], $attributes);
        }

        foreach ($permissions as $attributes) {
            AuthorizationPermission::query()->firstOrCreate(['slug' => $attributes['slug']], $attributes);
        }

        $role = AuthorizationRole::query()->where('slug', 'super-admin')->first();
        $permissionIds = AuthorizationPermission::query()->pluck('id')->all();
        if ($role !== null) {
            foreach ($permissionIds as $permissionId) {
                AuthorizationRolePermission::query()->firstOrCreate([
                    'role_id' => $role->id,
                    'permission_id' => (string) $permissionId,
                ]);
            }
        }

        if (! Schema::hasTable('users') || $role === null) {
            return;
        }

        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if (! is_string($firstUserId) || $firstUserId === '') {
            return;
        }

        AuthorizationRoleAssignment::query()->firstOrCreate([
            'role_id' => $role->id,
            'assignable_type' => 'Modules\\Users\\Domain\\Models\\User',
            'assignable_id' => $firstUserId,
            'organization_id' => null,
        ]);
    }
}
