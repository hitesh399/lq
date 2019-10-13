<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PermissionTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('title')->default('');
            $table->enum('is_public', ['Y', 'N'])->default('N');
            $table->unsignedInteger('permission_group_id');
            $table->foreign('permission_group_id')
                ->references('id')->on('permission_groups')
                ->onDelete('cascade');
            $table->json('encrypted')->nullable();
            $table->string('description', 255)->default('');
            $table->json('limitations')->nullable();
            $table->json('client_ids')->nullable();
            $table->json('specific_role_ids')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
