<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->unique(['role_id', 'permission_id']);
        });


        /* Schema::table('role_permissions', function (Blueprint $table) {

            $table->dropIndex('role_permissions_role_id_foreign');
        }); */

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::statement('ALTER TABLE `role_permissions` DROP INDEX `role_permissions_role_id_permission_id_unique`, ADD INDEX role_permissions_role_id_foreign (role_id)');
    }
}
