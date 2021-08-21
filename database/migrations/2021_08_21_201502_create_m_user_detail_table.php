<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ユーザーマスタ詳細
 */
class CreateMUserDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mUserDetail', function (Blueprint $table) {

            $table->string('companyId', 20);
            $table->string('contractPlanId', 20);
            $table->string('userId', 20);
            $table->string('password', 20);
            $table->string('name', 20);
            $table->string('departmentJob', 20)->nullable();
            $table->string('mail', 255);
            $table->dateTime('logoutDatetime');
            $table->boolean('lockFlg')->default(false);
            $table->boolean('delFlg')->default(false);
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->index(['companyId', 'contractPlanId', 'userId']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mUserDetail');
    }
}
