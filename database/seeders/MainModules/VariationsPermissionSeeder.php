<?php

// database/seeders/MainModules/VariationsPermissionSeeder.php
namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;

class VariationsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::generateFor('variations'); // browse, read, edit, add, delete
        Permission::generateFor('variation_sections');
        Permission::generateFor('variation_items');
    }
}
