<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use TCG\Voyager\Models\Role;

class UserController extends Controller
{
    protected function userModelClass(): string
    {
        return config('auth.providers.users.model');
    }

    protected function assignableRolesForCurrentUser()
    {
        $allowedIds = RoleAssignableController::assignableRoleIdsForUser(auth()->user());
        return Role::whereIn('id', $allowedIds)->orderBy('display_name')->orderBy('name')->get();
    }

    /** Index */
    public function index(Request $request)
    {
        $userClass = $this->userModelClass();
        /** @var Model $userModel */
        $userModel = new $userClass;

        $q = $userClass::query();

        // search
        if ($term = trim($request->get('q', ''))) {
            $q->where(function ($s) use ($term) {
                $s->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }

        // role filter (supports role_id or roles pivot)
        if ($roleId = $request->get('role_id')) {
            if (Schema::hasColumn($userModel->getTable(), 'role_id')) {
                $q->where('role_id', $roleId);
            } else {
                $q->whereHas('roles', function ($rq) use ($roleId) {
                    $rq->where('role_id', $roleId);
                });
            }
        }

        // status (optional column)
        if (Schema::hasColumn($userModel->getTable(), 'is_active')) {
            if ($request->get('status') === 'active') {
                $q->where('is_active', true);
            }
            if ($request->get('status') === 'inactive') {
                $q->where('is_active', false);
            }
        }

        $users = $q->orderByDesc('id')->paginate(20);

        $roles = Role::orderBy('display_name')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /** Create */
    public function create()
    {
        $roles = $this->assignableRolesForCurrentUser();
        return view('admin.users.create', compact('roles'));
    }

    /** Store */
    public function store(Request $request)
    {
        $userClass = $this->userModelClass();
        /** @var Model $userModel */
        $userModel = new $userClass;

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:190'],
            'email'                 => ['required', 'email', 'max:190', 'unique:' . $userModel->getTable() . ',email'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
            'role_id'               => ['required', 'integer', Rule::exists('roles', 'id')],
            'is_active'             => ['nullable', 'boolean'],
        ]);

        /** @var Model $user */
        $user = new $userClass;
        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);

        if (Schema::hasColumn($user->getTable(), 'is_active')) {
            $user->is_active = (bool)($data['is_active'] ?? true);
        }
        if (Schema::hasColumn($user->getTable(), 'role_id')) {
            $user->role_id = (int)$data['role_id'];
        }

        $user->save();

        // If many-to-many roles() exists, sync it as well
        if (method_exists($user, 'roles')) {
            $user->roles()->sync([(int)$data['role_id']]);
        }

        return redirect()->route('voyager.users.edit', $user->id)->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /** Edit */
    public function edit($id)
    {
        $userClass = $this->userModelClass();
        /** @var Model $user */
        $user = $userClass::findOrFail($id);

        $roles = $this->assignableRolesForCurrentUser();

        // Current role id for select (supports either relation style)
        $currentRoleId = null;
        if (Schema::hasColumn($user->getTable(), 'role_id')) {
            $currentRoleId = $user->role_id;
        } elseif (method_exists($user, 'roles')) {
            $currentRoleId = optional($user->roles()->pluck('roles.id')->first());
        }

        return view('admin.users.edit', compact('user', 'roles', 'currentRoleId'));
    }

    /** Update */
    public function update(Request $request, $id)
    {
        $userClass = $this->userModelClass();
        /** @var Model $user */
        $user = $userClass::findOrFail($id);

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:190'],
            'email'                 => ['required', 'email', 'max:190', Rule::unique($user->getTable(), 'email')->ignore($user->id)],
            'password'              => ['nullable', 'string', 'min:6', 'confirmed'],
            'role_id'               => ['required', 'integer', Rule::exists('roles', 'id')],
            'is_active'             => ['nullable', 'boolean'],
        ]);

        // Prevent deleting own admin privileges logic can be added here if needed

        $user->name  = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if (Schema::hasColumn($user->getTable(), 'is_active')) {
            $user->is_active = (bool)($data['is_active'] ?? false);
        }
        if (Schema::hasColumn($user->getTable(), 'role_id')) {
            $user->role_id = (int)$data['role_id'];
        }

        $user->save();

        if (method_exists($user, 'roles')) {
            $user->roles()->sync([(int)$data['role_id']]);
        }

        return redirect()->route('voyager.users.edit', $user->id)->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /** Destroy */
    public function destroy(Request $request, $id)
    {
        $userClass = $this->userModelClass();
        /** @var Model $user */
        $user = $userClass::findOrFail($id);

        if (auth()->id() === (int)$user->id) {
            return response()->json(['status' => false, 'message' => 'لا يمكنك حذف المستخدم الحالي'], 422);
        }

        if (method_exists($user, 'roles')) {
            $user->roles()->detach();
        }

        $user->delete();

        return response()->json(['status' => true]);
    }
}
