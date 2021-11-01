<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DBサイズアップ
 */
class ModfiySizeup20211101 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserDetail', function (Blueprint $table) {
            $table->string('departmentJob', 100)->change();
        });

        Schema::table('mPerson', function (Blueprint $table) {
            $table->string('departmentJob', 100)->change();
            $table->string('department', 100)->change();
            $table->text('address')->change();
            $table->string('infoSource', 100)->change();
        });

        Schema::table('mCorporation', function (Blueprint $table) {
            $table->text('address')->change();
            $table->string('infoSource', 100)->change();
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
            $table->string('departmentJob', 20)->change();
        });

        Schema::table('mPerson', function (Blueprint $table) {
            $table->string('departmentJob', 20)->change();
            $table->string('department', 20)->change();
            $table->string('address', 100)->change();
            $table->string('infoSource', 20)->change();
        });

        Schema::table('mCorporation', function (Blueprint $table) {
            $table->string('address', 100)->change();
            $table->string('infoSource', 20)->change();
        });

    }
}
