<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * mUserDetail
 *  logoutDatetimeをloginDatetimeに変更
 *
 */
class ModifyMUserDetail20210903 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserDetail', function (Blueprint $table) {
            $table->renameColumn('logoutDatetime', 'loginDatetime');

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
            $table->renameColumn('loginDatetime', 'logoutDatetime');

        });

    }
}
