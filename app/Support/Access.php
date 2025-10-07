<?php

namespace App\Support;

use App\Models\User;

class Access
{
    public static function isSuper(?User $user): bool
    {
        if (!$user) return false;
        if (method_exists($user, 'hasPermission') && $user->hasPermission('super_admin')) return true;
        if (method_exists($user, 'roles_all')) {
            return $user->roles_all()->where('name', 'super_admin')->exists();
        }
        if (method_exists($user, 'roles')) {
            return $user->roles()->where('name', 'super_admin')->exists();
        }
        if (method_exists($user, 'role') && $user->role) {
            return $user->role->name === 'super_admin';
        }
        return false;
    }
}
