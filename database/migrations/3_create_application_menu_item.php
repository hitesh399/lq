<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationMenuItem extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('application_menu_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->unique();
            $table->string('title')->default('');
            $table->integer('menu_order')->default(0);
            $table->enum('show_in_menu', ['Yes', 'No'])->default('Yes');
            $table->text('description')->nullable();
            $table->bigInteger('application_menu_id')->unsigned();
            $table->foreign('application_menu_id')
                ->references('id')->on('application_menus')
                ->onDelete('cascade');
            $table->json('permission_ids')->nullable();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')
                ->references('id')->on('application_menu_items')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('application_menu_items');
    }
}
