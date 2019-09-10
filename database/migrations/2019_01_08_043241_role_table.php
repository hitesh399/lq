<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
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

            $table->string('title')->default('');
            $table->string('description', 800)->default('');
            $table->json('portal')->nullable()->comment('To define the role access portal backend, frontend or mobile panel.');
            $table->enum('choosable', ['Y','N'])->default('Y')->comment('TO define is role assignable or only manage the category.');
            $table->string('landing_page')->default('')->comment('The url to redirect the user after login.');
            $table->unique(['name','parent_role_id']);
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
        Schema::dropIfExists('roles');
    }
}
