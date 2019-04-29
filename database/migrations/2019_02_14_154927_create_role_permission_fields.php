<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolePermissionFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permission_fields', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('role_id');
            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->onDelete('cascade');

            $table->unsignedInteger('role_permission_id');
            $table->foreign('role_permission_id')
                ->references('id')->on('role_permissions')
                ->onDelete('cascade');

            $table->unsignedInteger('permission_field_id');
            $table->foreign('permission_field_id')
                ->references('id')->on('permission_fields')
                ->onDelete('cascade');

            $table->unsignedInteger('permission_id');
            $table->foreign('permission_id')
                ->references('id')->on('permissions')
                ->onDelete('cascade');

            $table->unique(['role_id','permission_field_id']);
            $table->string('authority')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permission_fields');
    }
}
