<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * inputName、dispNameのサイズ変更(20 -> 130)
 */
class ModifyMPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mPerson', function (Blueprint $table) {
            $table->string('inputName', 130)->change();
            $table->string('dispName', 130)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mPerson', function (Blueprint $table) {
            $table->string('inputName', 20)->change();
            $table->string('dispName', 20)->change();
        });
    }
}
