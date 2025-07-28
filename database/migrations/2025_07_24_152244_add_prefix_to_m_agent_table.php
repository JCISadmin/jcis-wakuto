<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrefixToMAgentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mAgent', function (Blueprint $table) {
            // prefixカラムを追加
            $table->string('prefix', 20)->nullable()->comment('販売店・代理店Prefix')->after('status');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mAgent', function (Blueprint $table) {
            $table->dropColumn('prefix');
        });
    }
}
