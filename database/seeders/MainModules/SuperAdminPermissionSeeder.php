<?php


namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;
use App\Models\User;

class SuperAdminPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Create special permissions if missing
        $superAdminPerm = Permission::firstOrCreate(['key' => 'super_admin', 'table_name' => null]);
        $browseAdminPerm = Permission::firstOrCreate(['key' => 'browse_admin', 'table_name' => null]);

        // 2) Create the Super Admin role
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);

        // 3) Grant ALL current permissions to this role
        $role->permissions()->syncWithoutDetaching(Permission::pluck('id')->all());

        // 4) Optionally attach this role to a specific user (set your email!)
        $email = env('SUPER_ADMIN_EMAIL', 'admin@admin.com');
        if ($user = User::where('email', $email)->first()) {
            if (method_exists($user, 'roles_all')) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            } elseif (array_key_exists('role_id', $user->getAttributes())) {
                if (is_null($user->role_id)) {
                    $user->role_id = $role->id;
                    $user->save();
                }
            }
        }
    }
}
