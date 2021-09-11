<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 検索キーワード履歴
 */
class CreateTKeywordHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tKeywordHistory', function (Blueprint $table) {
            $table->string('companyId', 20);
            $table->string('contractPlanId', 20);
            $table->string('userId', 20);
            $table->string('hash', 255);
            $table->text('keyword');
            $table->dateTime('searchDate');

            $table->primary(['companyId', 'contractPlanId', 'userId', 'hash']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tKeywordHistory');
    }
}
