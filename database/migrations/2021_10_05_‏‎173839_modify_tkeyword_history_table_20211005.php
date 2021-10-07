<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTKeywordHistoryTable20211005 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tKeywordHistory', function (Blueprint $table) {
            $table->tinyInteger('chargeFlg')->nullable()->default(0)->after('searchDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tKeywordHistory', function (Blueprint $table) {
            $table->dropColumn('chargeFlg');
        });
    }
}
