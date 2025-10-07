<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $q = Role::query();

        // Search by name or display_name
        if ($term = trim($request->get('q', ''))) {
            $q->where(function ($s) use ($term) {
                $s->where('name', 'like', "%{$term}%")
                    ->orWhere('display_name', 'like', "%{$term}%");
            });
        }

        // Optional status filter if roles table has is_active
        if (Schema::hasColumn((new Role)->getTable(), 'is_active')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $q->where('is_active', true);
            }
            if ($status === 'inactive') {
                $q->where('is_active', false);
            }
        }

        $roles = $q->orderByDesc('id')->paginate(20);

        // Use Voyager route names directly in the blade, no need to pass moduleName
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $moduleName  = 'roles';
        $permissions = Permission::orderBy('table_name')->orderBy('key')->get();

        return view('admin.roles.create', compact('moduleName', 'permissions'));
    }

    /** Store new role */
    public function store(Request $request)
    {
        $data = $this->validateData($request, store: true);

        $role = new Role();
        $role->name         = $data['name'];
        $role->display_name = $data['display_name'] ?? null;

        // Set is_active only if the column exists
        if (Schema::hasColumn($role->getTable(), 'is_active')) {
            $role->is_active = (bool)($data['is_active'] ?? true);
        }

        $role->save();

        // Attach permissions
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('voyager.roles.edit', $role->id)
            ->with('success', 'تم إنشاء الدور بنجاح');
    }

    /** Edit form */
    public function edit(Role $role)
    {
        $moduleName      = 'roles'; // for route("voyager.$moduleName.*")
        $permissions     = Permission::orderBy('table_name')->orderBy('key')->get();
        $rolePermissions = $role->permissions()->get(); // or ->pluck('id')
        $allowDelete     = $role->name !== 'admin';     // protect the admin role (if you use it in the view)

        return view('admin.roles.edit', compact('moduleName', 'role', 'permissions', 'rolePermissions', 'allowDelete'));
    }

    /** Update role */
    public function update(Request $request, Role $role)
    {
        $data = $this->validateData($request, $role);

        // Optional: prevent renaming admin
        if ($role->name === 'admin' && $data['name'] !== 'admin') {
            return back()->withErrors(['name' => 'لا يمكن تغيير اسم دور admin.'])->withInput();
        }

        $role->name         = $data['name'];
        $role->display_name = $data['display_name'] ?? $role->display_name;

        // Only set if column exists on this model
        if (array_key_exists('is_active', $role->getAttributes())) {
            $role->is_active = (bool)($data['is_active'] ?? false);
        }

        $role->save();
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('voyager.roles.edit', $role->id)
            ->with('success', 'تم حفظ الدور بنجاح');
    }

    /** Delete role */
    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            abort(403, 'لا يمكن حذف دور admin');
        }
        $role->permissions()->detach();
        $role->delete();

        return response()->json(['status' => true]);
    }

    /** Shared validator (handles store vs update unique rule) */
    protected function validateData(Request $request, Role $role = null, bool $store = false): array
    {
        $unique = $store
            ? 'unique:roles,name'
            : 'unique:roles,name,' . ($role?->id ?? 'NULL');

        return $request->validate([
            'name'          => ['required', 'string', 'max:190', $unique],
            'display_name'  => ['nullable', 'string', 'max:190'],
            'is_active'     => ['nullable', 'boolean'],
            'permissions'   => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);
    }
}
