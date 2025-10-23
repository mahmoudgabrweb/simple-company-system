<?php

namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;

class SupplierPaymentModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modelName = "supplier_payments";

        // Permissions
        Permission::generateFor($modelName);

        $upperCase = "Supplier Payments";
        $upperCaseSingular = "SupplierPayment";
        $controllerName = "SupplierPaymentController";
        $iconClass = "bx bx-credit-card";

        // Data Type
        $dataType = DataType::firstOrNew(["name" => $modelName]);
        if (!$dataType->exists) {
            $dataType->fill([
                'slug' => $modelName,
                'display_name_singular' => $upperCaseSingular,
                'display_name_plural' => $upperCase,
                'icon' => $iconClass,
                'model_name' => "App\\Models\\$upperCaseSingular",
                'controller' => "App\\Http\\Controllers\\Admin\\$controllerName",
                'generate_permissions' => 1,
                'description' => '',
                'server_side' => 1,
                'details' => '{"order_column":"id","order_display_column":"id","order_direction":"desc","default_search_key":"title"}',
            ])->save();
        }

        // Menu Item
        $menu = Menu::where('name', 'admin')->firstOrFail();
        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => $upperCase,
            'url' => '',
            'route' => "voyager.$modelName.index",
        ]);

        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => $iconClass,
                'color' => null,
                'parent_id' => null,
                'order' => 27,
            ])->save();
        }
    }
}
