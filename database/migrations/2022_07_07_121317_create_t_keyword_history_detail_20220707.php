<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTKeywordHistoryDetail20220707 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tKeywordHistoryDetail', function (Blueprint $table) {
            $table->string('companyId', 20);
            $table->string('searchMonth', 6);
            $table->integer('searchCount');

            $table->primary(['companyId', 'searchMonth']);
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
    }
}
