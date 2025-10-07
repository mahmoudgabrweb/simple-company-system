<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Role;

class RoleAssignableController extends Controller
{
    /** Show matrix page */
    public function index()
    {
        $roles = Role::orderBy('display_name')->orderBy('name')->get();

        // Build current mapping: role_id => [assignable_role_ids]
        $rows = DB::table('role_assignables')
            ->select('role_id', DB::raw('GROUP_CONCAT(assignable_role_id) as ids'))
            ->groupBy('role_id')
            ->pluck('ids', 'role_id')
            ->map(function ($csv) {
                return collect(explode(',', (string)$csv))
                    ->filter(fn($v) => $v !== '')
                    ->map(fn($v) => (int)$v)
                    ->all();
            })
            ->toArray();

        return view('admin.roles_control.index', compact('roles', 'rows'));
    }

    /** Save matrix */
    public function store(Request $request)
    {
        $data = $request->validate([
            'assignable'   => ['array'],
            'assignable.*' => ['array'],
        ]);

        // Collect all roles and normalize incoming payload
        $allRoleIds   = \TCG\Voyager\Models\Role::pluck('id')->all();
        $assignable   = $data['assignable'] ?? [];
        $adminRoleId  = \TCG\Voyager\Models\Role::where('name', 'admin')->value('id');

        // Ensure we have an entry for every role (unchecked rows may be missing)
        foreach ($allRoleIds as $rid) {
            $ids = collect($assignable[$rid] ?? [])
                ->map(fn($v) => (int) $v)
                ->filter()
                ->unique()
                ->values();

            // Optional: allow self-assign (comment out if you don’t want it)
            if (!$ids->contains($rid)) {
                $ids->push($rid);
            }

            $assignable[$rid] = $ids->all();
        }

        // Admin role: full access (optional)
        if ($adminRoleId) {
            $assignable[$adminRoleId] = $allRoleIds;
        }

        DB::transaction(function () use ($assignable) {
            foreach ($assignable as $roleId => $ids) {
                $ids = collect($ids)->unique()->values();

                // 1) Insert missing pairs (ignore duplicates)
                $rows = $ids->map(fn($to) => [
                    'role_id' => (int) $roleId,
                    'assignable_role_id' => (int) $to,
                ])->all();

                if (!empty($rows)) {
                    DB::table('role_assignables')->insertOrIgnore($rows);
                }

                // 2) Remove any extra pairs not selected in the matrix
                DB::table('role_assignables')
                    ->where('role_id', (int) $roleId)
                    ->when(
                        $ids->isNotEmpty(),
                        fn($q) => $q->whereNotIn('assignable_role_id', $ids->all()),
                        fn($q) => $q
                    ) // if empty, delete all for this role
                    ->delete();
            }
        });

        return redirect()->route('voyager.role_assignables.index')->with('success', 'تم حفظ التحكم بالأدوار');
    }

    /** Helper for other controllers: returns Collection<int> of allowed role IDs for given user */
    public static function assignableRoleIdsForUser($user)
    {
        // Super admin sees all
        $isAdmin = false;
        if (property_exists($user, 'role_id') && $user->role_id) {
            $isAdmin = optional($user->role)->name === 'admin';
        }
        if (!$isAdmin && method_exists($user, 'roles')) {
            $isAdmin = $user->roles()->where('name', 'admin')->exists();
        }
        if ($isAdmin) {
            return Role::pluck('id');
        }

        // Gather current user's role IDs (supports role_id OR roles pivot)
        $currentRoleIds = collect();
        if (property_exists($user, 'role_id') && $user->role_id) {
            $currentRoleIds->push((int)$user->role_id);
        }
        if (method_exists($user, 'roles')) {
            $currentRoleIds = $currentRoleIds
                ->merge($user->roles()->pluck('roles.id'))
                ->unique()->values();
        }

        if ($currentRoleIds->isEmpty()) {
            return collect([]);
        }

        // Union of all assignables for those roles
        $allowed = DB::table('role_assignables')
            ->whereIn('role_id', $currentRoleIds->all())
            ->pluck('assignable_role_id')
            ->unique()
            ->values();

        // Optionally include creator's own roles
        $allowed = $allowed->merge($currentRoleIds)->unique()->values();

        return $allowed;
    }
}
