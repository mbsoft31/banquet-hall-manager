<?php

namespace Mbsoft\BanquetHallManager\Database\seeders;

use Illuminate\Database\Seeder;

class BhmRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        if (!class_exists(\Spatie\Permission\Models\Role::class) || !class_exists(\Spatie\Permission\Models\Permission::class)) {
            $this->command?->warn('Spatie permissions not installed; skipping BHM roles/permissions seeding.');
            return;
        }

        $guard = config('permission.defaults.guard', config('auth.defaults.guard', 'web'));

        $abilities = ['read', 'write', 'delete'];
        $permPrefixMap = (array) config('banquethallmanager.permissions');

        // Create permissions
        $permissions = [];
        foreach ($abilities as $ability) {
            $name = $permPrefixMap[$ability] ?? ('bhm.'.$ability);
            $permissions[$ability] = \Spatie\Permission\Models\Permission::query()->firstOrCreate(
                ['name' => $name, 'guard_name' => $guard]
            );
        }

        // Build role -> permissions mapping from config roles.* lists
        $rolesConfig = (array) config('banquethallmanager.roles');
        $roleToPerms = [];
        foreach ($abilities as $ability) {
            $rolesForAbility = (array) ($rolesConfig[$ability] ?? []);
            foreach ($rolesForAbility as $role) {
                $roleToPerms[$role] = $roleToPerms[$role] ?? [];
                $roleToPerms[$role][] = $permissions[$ability];
            }
        }

        // Ensure typical roles exist even if not in config
        foreach (['viewer','staff','manager','admin'] as $roleName) {
            $roleToPerms[$roleName] = $roleToPerms[$roleName] ?? [];
        }

        // Create roles and sync permissions
        foreach ($roleToPerms as $roleName => $perms) {
            $role = \Spatie\Permission\Models\Role::query()->firstOrCreate(
                ['name' => $roleName, 'guard_name' => $guard]
            );
            $role->syncPermissions($perms);
        }

        $this->command?->info('BHM roles and permissions seeded.');
    }
}

