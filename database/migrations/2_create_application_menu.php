<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationMenu extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('application_menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->json('client_ids')->nullable();
            $table->json('role_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('application_menus');
    }
}
