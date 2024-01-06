<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\RolePermission\Entities\Permission;

class AddNewPermissionForNewFeature02 extends Migration
{
    public function up()
    {
        $routes = [
            ['name' => 'Cookie/GDPR Setting', 'route' => 'setting.cookieSetting', 'type' => 2, 'parent_route' => 'settings'],
            ['name' => 'Theme Font', 'route' => 'appearance.themes-font.index', 'type' => 2, 'parent_route' => 'appearance'],
        ];

        foreach ($routes as $route) {

            $section_id = 1;
            if ($route['parent_route']) {
                $parent = Permission::where('route', $route['parent_route'])->first();
                if ($parent) {
                    $section_id = $parent->section_id;
                }
            }
            Permission::updateOrCreate([
                'route' => $route['route'],
            ], [
                    'route' => $route['route'],
                    'name' => $route['name'],
                    'type' => $route['type'],
                    'parent_route' => $route['parent_route'],
                    'old_name' => $route['name'],
                    'old_type' => $route['type'],
                    'old_parent_route' => $route['parent_route'],
                    'ecommerce' => 0,
                    'backend' => $route['backend'] ?? 1,
                    'section_id' => $section_id,
                ]
            );
        }
    }

    public function down()
    {
        //
    }
}
