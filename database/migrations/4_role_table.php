<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RoleTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);

            $table->unsignedInteger('parent_role_id')->nullable();
            $table->foreign('parent_role_id')
                ->references('id')->on('roles')
                ->onDelete('cascade');
            $table->json('client_ids')->nullable();

            $table->string('title')->default('');
            $table->string('description', 800)->default('');
            $table->enum('choosable', ['Y', 'N'])->default('Y')->comment('TO define is role assignable or only manage the category.');
            $table->json('settings')->nullable();
            $table->unique(['name', 'parent_role_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
