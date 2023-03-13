<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMUserCompany20230310 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserCompany', function (Blueprint $table) {
            $table->tinyInteger('freeFlg')->default(0)->after('paymentTerm');
            $table->integer('freePeriod')->nullable()->default(0)->after('freeFlg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mUserCompany', function (Blueprint $table) {
            $table->dropColumn('freeFlg');
            $table->dropColumn('freePeriod');
        });
    }
}
