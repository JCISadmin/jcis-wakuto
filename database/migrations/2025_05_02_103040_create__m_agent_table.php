<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMAgentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mAgent', function (Blueprint $table) {
            $table->increments('id');
            $table->string('distributor_cd', 20)->comment('販売店CD');
            $table->string('agent_cd', 20)->comment('代理店CD');
            $table->string('level', 3)->comment('契約レベル');
            $table->tinyInteger('status')->comment('契約状態');
            $table->string('name', 20)->comment('企業名');
            $table->string('postCode', 7)->comment('郵便番号');
            $table->string('pref_code', 3)->comment('都道府県コード');
            $table->string('address', 200)->comment('住所');
            $table->string('tel', 20)->comment('電話番号');
            $table->string('mailCompanyName', 20)->nullable()->comment('メール宛名');
            $table->string('homePageUrl', 100)->nullable()->comment('企業ホームページ');
            $table->dateTime('createDate')->nullable()->change()->comment('登録日');
            $table->dateTime('updateDate')->nullable()->change()->comment('更新日');

            $table->index(['distributor_cd', 'agent_cd']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mAgent');
    }
}
