<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 2要素トークン管理
 */
class CreateT2FactTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t2FactToken', function (Blueprint $table) {
            $table->string('tokenId');
            $table->string('authCode');
            $table->dateTime('expireDate');

            $table->index(['tokenId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t2FactToken');
    }
}
