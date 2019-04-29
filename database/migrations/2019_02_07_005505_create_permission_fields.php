<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_fields', function (Blueprint $table) {
            $table->increments('id');

            $table->unSignedInteger('permission_id');

            $table->foreign('permission_id')
                ->references('id')->on('permissions')
                ->onDelete('CASCADE');

            $table->string('title')->defalt('');
            $table->string('client_field', 190);

            $table->unique(['permission_id','client_field']);
            $table->json('table_columns');

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
        Schema::dropIfExists('permission_fields');
    }
}
