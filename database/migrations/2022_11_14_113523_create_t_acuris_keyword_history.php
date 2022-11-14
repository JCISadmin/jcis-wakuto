<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAcurisKeywordHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tAcurisKeywordHistory', function (Blueprint $table) {

            $table->string('companyId', 20);
            $table->string('userId', 20);
            $table->date('searchDate');
            $table->integer('searchCount');
            $table->integer('detailSearchCount');
            $table->integer('errSearchCount');
            $table->integer('errDetailSearchCount');

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
        Schema::dropIfExists('tAcurisKeywordHistory');
    }
}
