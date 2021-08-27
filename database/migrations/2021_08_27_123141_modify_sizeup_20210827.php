<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 20210827 仕様変更対応
 */
class ModifySizeup20210827 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserCompany', function (Blueprint $table) {
            $table->string('name', 40)->change();
        });

        Schema::table('mCorporation', function (Blueprint $table) {
            $table->string('industry', 60)->change();
            $table->string('filename', 80)->change();
        });

        Schema::table('mPerson', function (Blueprint $table) {
            $table->string('filename', 80)->change();
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
            $table->string('name', 20)->change();
        });

        Schema::table('mCorporation', function (Blueprint $table) {
            $table->string('industry', 20)->change();
            $table->string('filename', 50)->change();
        });

        Schema::table('mPerson', function (Blueprint $table) {
            $table->string('filename', 50)->change();
        });

    }
}
