<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDemoImportMenu extends Migration
{
    public function up()
    {
        try {
            \Modules\RolePermission\Entities\Permission::where('route', 'appearance.themes.demo')->delete();
        } catch (\Exception $e) {

        }
        $sidebar = new \Modules\SidebarManager\Http\Controllers\SidebarManagerController();
        $sidebar->resetMenu();
    }

    public function down()
    {
        //
    }
}
