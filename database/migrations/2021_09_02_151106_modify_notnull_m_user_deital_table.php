<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * mUserDetailのlogoutDatetimeをNullOKへ
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
            $table->dateTime('logoutDatetime')->nullable()->change();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
