<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SiteConfig extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('site_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->text('data');
            $table->enum('autoload', ['1', '0'])->default('0');
            $table->enum('config_type', ['global', 'private'])->default('global');
            $table->string('config_group', 100)->nullable();
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('site_config');
    }
}
