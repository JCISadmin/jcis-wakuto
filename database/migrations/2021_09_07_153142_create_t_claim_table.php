<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 請求テーブル
 */
class CreateTClaimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tClaim', function (Blueprint $table) {
            $table->string('companyId', 20);
            $table->string('claimMonth', 6);
            $table->string('claimNo', 20)->nullable();
            $table->integer('price')->nullable();
            $table->date('claimDate')->nullable();
            $table->date('paymentDate')->nullable();
            $table->string('adjustNote', 20)->nullable();
            $table->integer('adjustPrice')->nullable();
            $table->tinyInteger('claimStatus')->nullable();
            $table->tinyInteger('paymentStatus')->nullable();
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->primary(['companyId', 'claimMonth']);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tClaim');
    }
}
