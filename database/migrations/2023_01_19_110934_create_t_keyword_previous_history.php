<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTKeywordPreviousHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tKeywordPreviousHistory', function (Blueprint $table) {
            $table->string('companyId', 20);
            $table->string('contractPlanId', 20);
            $table->string('userId', 20);
            $table->string('hash', 255);
            $table->integer('seqNo');
            $table->text('keyword');
            $table->dateTime('searchDate');
            $table->tinyInteger('chargeFlg')->nullable()->default(0);

            $table->primary(['companyId', 'contractPlanId', 'userId', 'hash', 'seqNo'],'PRIMARY_NAME');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tKeywordPreviousHistory');

        Schema::table('tKeywordHistory', function (Blueprint $table) {
            $table->dropColumn('freeSearchCount');
        });
    }
}
