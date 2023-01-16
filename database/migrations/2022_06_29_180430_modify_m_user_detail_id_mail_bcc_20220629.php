<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMUserDetailIdMailBcc20220629 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserDetail', function (Blueprint $table) {
            $table->string('idMailBcc', 255)->nullable()->after('mail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mUserDetail', function (Blueprint $table) {
            $table->dropColumn('idMailBcc');
        });
    }
}
