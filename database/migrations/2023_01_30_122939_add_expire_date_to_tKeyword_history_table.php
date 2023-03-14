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
            $table->string('expireDate', 20)->after('hash')->default('20230101000000000');

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

            $table->dropPrimary(['companyId', 'contractPlanId', 'userId', 'hash','expireDate']);
            $table->primary(['companyId', 'contractPlanId', 'userId', 'hash'], 'tKeywordHistory_table_primary');

            $table->dropColumn('expireDate');
        });
    }
}
