<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleAccessTypeInOauthClients extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->enum(
                'role_access_type', ['one_at_time', 'many_at_time']
            )->default('one_at_time')->after('secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn('role_access_type');
        });
    }
}
