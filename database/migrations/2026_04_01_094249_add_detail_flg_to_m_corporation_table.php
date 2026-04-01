<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailFlgToMCorporationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mCorporation', function (Blueprint $table) {
            $table->tinyInteger('detail_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
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
            $table->dropColumn('detail_flg', 'del_flg');
        });
    }
}
