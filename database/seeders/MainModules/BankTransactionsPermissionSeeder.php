<?php


namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;

class BankTransactionsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modelName = "bank_transactions";

        // Permissions
        Permission::generateFor($modelName);

        $upperCase = "Bank Transactions";
        $upperCaseSingular = "BankTransaction";
        $controllerName = "BankTransactionController";
        $iconClass = "voyager-dollar";

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
                'details' => json_encode([
                    "order_column" => "txn_date",
                    "order_display_column" => "txn_date",
                    "order_direction" => "desc",
                    "default_search_key" => "reference"
                ]),
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
                'order' => 45, // adjust to your sidebar ordering
            ])->save();
        }
    }
}
