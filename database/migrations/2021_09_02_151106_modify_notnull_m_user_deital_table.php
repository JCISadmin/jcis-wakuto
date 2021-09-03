<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * mUserDetailのloginDatetimeをNullOKへ
 */
class ModifyNotnullMUserDeitalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserDetail', function (Blueprint $table) {
            $table->dateTime('loginDatetime')->nullable()->change();

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
            $table->dateTime('loginDatetime')->nullable(false)->change();

        });
    }
}
