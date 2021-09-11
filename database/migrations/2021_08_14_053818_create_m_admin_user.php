<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 管理ユーザーテーブルの作成
 */
class CreateMAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mAdminUser', function (Blueprint $table) {

            $table->string('userId', 20);
            $table->string('userName', 20);
            $table->string('password', 20);
            $table->string('mail', 255)->nullable();
            $table->boolean('lockFlg')->default(false);
            $table->boolean('delFlg')->default(false);
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->primary(['userId']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mAdminUser');
    }
}
