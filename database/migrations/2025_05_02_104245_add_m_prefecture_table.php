<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMPrefectureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 都道府県コードカラム追加
        Schema::table('mPrefecture', function (Blueprint $table) {
            $table->integer('pref_code')->length(3)->nullable()->comment('都道府県コード')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mPrefecture', function (Blueprint $table) {
            $table->dropColumn('pref_code');
        });
    }
}
