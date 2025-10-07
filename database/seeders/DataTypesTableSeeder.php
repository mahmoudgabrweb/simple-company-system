<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class DataTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        // NOTE: Key on 'name' (table name). Always set BOTH 'name' and 'slug' before save.

        $this->ensureDataType('users', [
            'display_name_singular' => __('voyager::seeders.data_types.user.singular'),
            'display_name_plural'   => __('voyager::seeders.data_types.user.plural'),
            'icon'                  => 'voyager-person',
            // If your app uses App\Models\User, change the next line accordingly.
            'model_name'            => 'TCG\\Voyager\\Models\\User',
            'policy_name'           => 'TCG\\Voyager\\Policies\\UserPolicy',
            'controller'            => 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController',
            'generate_permissions'  => 1,
            'description'           => '',
        ]);

        $this->ensureDataType('menus', [
            'display_name_singular' => __('voyager::seeders.data_types.menu.singular'),
            'display_name_plural'   => __('voyager::seeders.data_types.menu.plural'),
            'icon'                  => 'voyager-list',
            'model_name'            => 'TCG\\Voyager\\Models\\Menu',
            'controller'            => '',
            'generate_permissions'  => 1,
            'description'           => '',
        ]);

        $this->ensureDataType('roles', [
            'display_name_singular' => __('voyager::seeders.data_types.role.singular'),
            'display_name_plural'   => __('voyager::seeders.data_types.role.plural'),
            'icon'                  => 'voyager-lock',
            'model_name'            => 'TCG\\Voyager\\Models\\Role',
            'controller'            => 'TCG\\Voyager\\Http\\Controllers\\VoyagerRoleController',
            'generate_permissions'  => 1,
            'description'           => '',
        ]);
    }

    private function ensureDataType(string $table, array $attrs): void
    {
        // Key by 'name' to avoid inserting with only 'slug'
        $dataType = DataType::firstOrNew(['name' => $table]);

        $payload = array_merge([
            'name'                  => $table,     // ensure present
            'slug'                  => $table,     // ensure present
            'server_side'           => 1,
            'details'               => null,
            'order_column'          => null,
            'order_display_column'  => null,
            'order_direction'       => 'asc',
            'default_search_key'    => null,
            'scope'                 => null,
        ], $attrs);

        $dataType->fill($payload);
        $dataType->save();
    }
}
