<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMUserCompanyTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserCompany', function (Blueprint $table) {
            // テストユーザーフラグの追加
            $table->boolean('testFlg')->default(false)->comment('テストユーザーフラグ')->after('contractStatus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mUserCompany', function (Blueprint $table) {
            //
            $table->dropColumn('testFlg');
        });
    }
}
