<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpireDateTotKeywordHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tKeywordHistory', function (Blueprint $table) {
            $table->dateTime('expireDate')->after('hash')->default('2023-01-01');

            $table->dropPrimary(['companyId', 'contractPlanId', 'userId', 'hash']);
            $table->primary(['companyId', 'contractPlanId', 'userId', 'hash','expireDate'], 'tKeywordHistory_table_primary');
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
            $table->dropColumn('expireDate');
        });
    }
}
