<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleMenuItem extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('role_menu_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('role_id');
            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->onDelete('cascade');

            $table->unsignedBigInteger('menu_item_id');
            $table->foreign('menu_item_id')
                ->references('id')->on('application_menu_items')
                ->onDelete('cascade');
            $table->unique(['role_id', 'menu_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('role_menu_item');
    }
}
