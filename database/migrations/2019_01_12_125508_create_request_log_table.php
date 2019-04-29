<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_logs', function (Blueprint $table) {

            $table->increments('id');
            $table->text('url');
            $table->string('route_name')->default('');
            $table->string('request_method')->default('');

            $table->unsignedInteger('client_id')->nullable();
            $table->ipAddress('ip_address');

            $table->foreign('client_id')
                ->references('id')->on('oauth_clients')
                ->onDelete('cascade');

            $table->bigInteger('device_id')->unsigned()->nullable();
            $table->foreign('device_id')
                ->references('id')->on('devices')
                ->onDelete('cascade');

            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->enum('response_status', ['ok', 'exception'])->default('ok');
            $table->unsignedTinyInteger('status_code')->default(200);

            $table->json('request_headers')->nullable();
            $table->json('response_headers')->nullable();

            $table->json('request')->nullable();
            $table->json('response')->nullable();

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
        Schema::dropIfExists('request_logs');
    }
}
