<?php

namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;

class SiteServicesPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modelName = "site_services";

        // Generate permissions (browse, read, edit, add, delete)
        Permission::generateFor($modelName);

        $upperCase = "Site Services";
        $upperCaseSingular = "SiteService";
        $controllerName = "SiteServiceController";
        $iconClass = "voyager-list";

        // DataType (Voyager BREAD)
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
                    "order_column" => "display_order",
                    "order_display_column" => "title",
                    "order_direction" => "asc",
                    "default_search_key" => "title"
                ]),
            ])->save();
        }

        // Menu Item
        $menu = Menu::where('name', 'admin')->firstOrFail();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title' => $upperCase,
            'route' => "voyager.$modelName.index",
            'url' => '',
        ]);

        if (!$menuItem->exists) {
            $menuItem->fill([
                'target' => '_self',
                'icon_class' => $iconClass,
                'color' => null,
                'parent_id' => null,
                'order' => 46, // adjust ordering as needed
            ])->save();
        }
    }
}
