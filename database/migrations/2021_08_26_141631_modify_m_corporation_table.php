<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * inputName、dispNameのサイズ変更(20 -> 80)
 */
class ModifyMCorporationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mCorporation', function (Blueprint $table) {
            $table->string('inputName', 80)->change();
            $table->string('dispName', 80)->change();
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
            $table->string('inputName', 20)->change();
            $table->string('dispName', 20)->change();
        });

    }
}
