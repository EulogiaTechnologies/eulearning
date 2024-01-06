<?php

use Illuminate\Database\Migrations\Migration;
use Modules\RolePermission\Entities\Permission;

class AddNewPermissionForNewFeature01 extends Migration
{
    public function up()
    {


        $routes = [
            ['name' => 'Blog Setting', 'route' => 'blogs.setting.index', 'type' => 2, 'parent_route' => 'blogs'],
            ['name' => 'My Blog', 'route' => 'users.blog.index', 'type' => 1, 'parent_route' => null, 'backend' => 0],
        ];
        foreach ($routes as $route) {
            Permission::updateOrCreate([
                'route' => $route['route'],
            ], [
                    'name' => $route['name'],
                    'route' => $route['route'],
                    'parent_route' => $route['parent_route'],
                    'type' => $route['type'],
                    'ecommerce' => 0,
                    'backend' => $route['backend'] ?? 1,
                ]
            );
        }
    }

    public function down()
    {
        //
    }
}
