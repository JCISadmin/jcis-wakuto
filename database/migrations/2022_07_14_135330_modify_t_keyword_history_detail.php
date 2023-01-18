<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTKeywordHistoryDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('tKeywordHistoryDetail');

        Schema::create('tKeywordHistoryDetail', function (Blueprint $table) {
            $table->string('companyId', 20);
            $table->string('userId', 20);
            $table->date('searchDate');
            $table->integer('searchCount');

            $table->primary(['companyId', 'userId', 'searchDate']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tKeywordHistoryDetail');

        Schema::create('tKeywordHistoryDetail', function (Blueprint $table) {
            $table->string('companyId', 20);
            $table->string('searchMonth', 6);
            $table->integer('searchCount');

            $table->primary(['companyId', 'searchMonth']);
        });
    }
}
