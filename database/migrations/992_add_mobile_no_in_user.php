<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMobileNoInUser extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table(
            'users', function (Blueprint $table) {
                $table->string('mobile_no', 20)->nullable()->unique()->after('email_verified_at');
                $table->timestamp('mobile_no_verified_at')->nullable()->after('mobile_no');
                $table->string('timezone')->nullable();
                $table->softDeletes();
                $table->enum('status', ['active',  'inactive']);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mobile_no');
            $table->dropColumn('mobile_no_verified_at');
            $table->dropColumn('timezone');
            $table->dropColumn('status');
            $table->dropSoftDeletes();
        });
    }
}
