<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLandingPageUrlInRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $charset = Config::get('database.connections.mysql.charset');
        $collation = Config::get('database.connections.mysql.collation');

        \DB::statement("ALTER TABLE `roles` CHANGE `landing_page` `landing_portal` VARCHAR(100)
            CHARACTER SET {$charset} COLLATE {$collation} NOT NULL DEFAULT ''
            COMMENT 'Portal Name, where user will redirect after login';"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $charset = Config::get('database.connections.mysql.charset');
        $collation = Config::get('database.connections.mysql.collation');

        \DB::statement("ALTER TABLE `roles` CHANGE `landing_portal` `landing_page` VARCHAR(200)
            CHARACTER SET {$charset} COLLATE {$collation} NOT NULL DEFAULT ''
            COMMENT 'URL where user will redirect after login.';"
        );
    }
}
