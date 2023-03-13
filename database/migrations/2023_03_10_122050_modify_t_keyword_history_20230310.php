<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTKeywordHistory20230310 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tKeywordHistory', function (Blueprint $table) {
            $table->dropPrimary(['companyId', 'contractPlanId', 'userId', 'hash', 'expireDate']);
            $table->integer('seqNo')->after('hash');
            $table->primary(['companyId', 'contractPlanId', 'userId', 'hash', 'seqNo'], 'tKeywordHistory_table_primary');
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
            $table->dropPrimary(['companyId', 'contractPlanId', 'userId', 'hash', 'seqNo']);
            $table->dropColumn('seqNo');
            $table->primary(['companyId', 'contractPlanId', 'userId', 'hash', 'expireDate'], 'tKeywordHistory_table_primary');
        });
    }
}
