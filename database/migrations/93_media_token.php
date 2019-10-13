<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MediaToken extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('media_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token', 200)->unique();
            $table->string('file_name')->comment('File Name');
            $table->integer('file_size')->comment('File Size in bytes');
            $table->string('path', 100)->comment('Path where the file will upload.');
            $table->bigInteger('device_id')->unsigned()->comment('Unique Identification of device from which user is uploading the image.');
            $table->foreign('device_id')
                ->references('id')->on('devices')
                ->onDelete('cascade');

            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')
                ->references('id')->on('oauth_clients')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('media_tokens');
    }
}
