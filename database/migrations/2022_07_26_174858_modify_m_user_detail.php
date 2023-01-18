<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMUserDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserDetail', function (Blueprint $table) {
            $table->string('delMonth', 6)->nullable()->after('delFlg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mUserDetail', function (Blueprint $table) {
            $table->dropColumn('delMonth');
        });
    }
}
