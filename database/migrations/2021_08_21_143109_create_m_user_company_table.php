<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ユーザーマスタの作成
 */
class CreateMUserCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mUserCompany', function (Blueprint $table) {

            $table->string('companyId', 20);
            $table->string('name', 20)->nullable();
            $table->string('postCode', 7)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('tel', 20)->nullable();
            $table->string('staffName', 20)->nullable();
            $table->string('staffDepartmentJob', 20)->nullable();
            $table->string('staffTel', 20)->nullable();
            $table->string('staffMail', 255)->nullable();
            $table->string('claimName', 20)->nullable();
            $table->string('claimDepartmentJob', 20)->nullable();
            $table->string('claimTel', 20)->nullable();
            $table->text('claimMailTo')->nullable();
            $table->text('claimMailCc')->nullable();
            $table->string('chargeName', 20)->nullable();
            $table->string('chargeMail', 255)->nullable();
            $table->boolean('delFlg')->default(false);
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->index(['companyId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mUserCompany');
    }
}
