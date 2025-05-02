<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPrefCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mPrefCode', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pref_code', 20)->comment('都道府県コード');
            $table->string('pref_name', 20)->comment('都道府県名');
            $table->string('order', 20)->comment('表示順');
            $table->integer('createDate')->nullable()->change()->comment('登録日');
            $table->dateTime('updateDate')->nullable()->change()->comment('更新日');

            $table->index(['pref_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mPrefCode');
    }
}
