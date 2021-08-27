<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 20210827 仕様変更対応
 */
class ModifySizeup202108272 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mCorporation', function (Blueprint $table) {
            $table->string('department', 50)->change();

        });

        Schema::table('mPerson', function (Blueprint $table) {
            $table->string('inputKana', 130)->change();
            $table->string('dispKana', 130)->change();
            $table->string('departmentJob', 50)->change();
            $table->string('department', 50)->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mCorporation', function (Blueprint $table) {
            $table->string('department', 20)->change();

        });

        Schema::table('mPerson', function (Blueprint $table) {
            $table->string('inputKana', 20)->change();
            $table->string('dispKana', 20)->change();
            $table->string('departmentJob', 20)->change();
            $table->string('department', 20)->change();
        });

    }
}
