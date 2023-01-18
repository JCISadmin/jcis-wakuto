<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class tMngBatchAddSearchType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tMngBatch', function (Blueprint $table) {
            $table->string('searchType')->default('normal')->after('batchId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tMngBatch', function (Blueprint $table) {
            $table->dropColumn('searchType');
        });
    }
}
