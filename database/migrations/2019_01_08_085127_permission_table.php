<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name', 100)->unique();

            $table->unsignedInteger('permission_group_id');
            $table->foreign('permission_group_id')
                ->references('id')->on('permission_groups')
                ->onDelete('cascade');

            $table->string('description', 255)->default('');
            $table->json('limitations')->nullable();

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
        Schema::dropIfExists('permissions');
    }
}
