<?php

namespace Mbsoft\BanquetHallManager\Support\Permissions;

use Illuminate\Support\Facades\Schema;

class AbilityResolver
{
    public static function canRead($user): bool
    {
        return self::check($user, 'read');
    }

    public static function canWrite($user): bool
    {
        return self::check($user, 'write');
    }

    public static function canDelete($user): bool
    {
        return self::check($user, 'delete');
    }

    protected static function check($user, string $ability): bool
    {
        if (!$user) {
            return false;
        }

        $permMap = (array) config('banquethallmanager.permissions');

        // If Spatie permissions are available, use them first
        if (method_exists($user, 'hasPermissionTo') && Schema::hasTable('permissions')) {
            $perm = $permMap[$ability] ?? null;
            if ($perm) {
                try {
                    return $user->hasPermissionTo($perm) || $user->can($perm);
                } catch (\Throwable) {
                    // Fall back if permission tables are not migrated in this environment
                }
            }
        }

        // Roles mapping (Spatie roles or simple role column)
        $allowed = (array) config("banquethallmanager.roles.{$ability}");
        // Spatie roles (prefer, but fall back to simple role column if not assigned)
        if (method_exists($user, 'hasAnyRole') && Schema::hasTable('roles') && Schema::hasTable('model_has_roles')) {
            try {
                if ($user->hasAnyRole($allowed)) {
                    return true;
                }
            } catch (\Throwable) {
                // ignore and fall through to simple role column
            }
        }
        // Simple role column fallback (works for tests or non-Spatie setups)
        if (isset($user->role)) {
            return in_array((string) $user->role, $allowed, true);
        }
        // No permission/role system detected: default deny
        return false;
    }
}
