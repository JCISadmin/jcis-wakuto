<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 20211029 仕様変更対応
 */
class ModifySizeup20211029 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserCompany', function (Blueprint $table) {
            $table->string('staffDepartmentJob', 100)->change();
            $table->string('claimDepartmentJob', 100)->change();
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
            $table->string('staffDepartmentJob', 20)->change();
            $table->string('claimDepartmentJob', 20)->change();
        });

    }
}
