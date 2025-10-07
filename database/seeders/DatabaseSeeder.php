<?php

namespace Database\Seeders;

use Database\Seeders\MainModules\DatabaseSeeder as MainModulesDatabaseSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
             MenusTableSeeder::class,
             MenuItemsTableSeeder::class,
             DataTypesTableSeeder::class,
             DataRowsTableSeeder::class,
             RolesTableSeeder::class,
             PermissionsTableSeeder::class,
             PermissionRoleTableSeeder::class,
             SettingsTableSeeder::class,
             UsersTableSeeder::class,
             PermissionRoleTableSeeder::class,
        ]);

        $this->call([
            MainModulesDatabaseSeeder::class,
            // ProductPermissionSeeder::class,
        ]);

        $this->call([
            // DummyDataDatabaseSeeder::class,
        ]);
    }
}
