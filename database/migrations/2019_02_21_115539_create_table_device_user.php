<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDeviceUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_user', function (Blueprint $table) {

            $table->increments('id');

            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('CASCADE');

            $table->bigInteger('device_id')->unsigned();
            $table->foreign('device_id')
                ->references('id')->on('devices')
                ->onDelete('CASCADE');

            $table->unSignedInteger('login_index');
            $table->enum('active', ['Yes','No'])->default('Yes');
            $table->unique(['user_id', 'device_id']);
            $table->json('settings')->nullable();
            $table->enum('revoked', ['0','1'])->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_user');
    }
}
