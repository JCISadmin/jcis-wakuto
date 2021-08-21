<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 契約プランマスタ
 */
class CreateMContractPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mContractPlan', function (Blueprint $table) {

            $table->string('contractPlanId', 20);
            $table->string('name', 20);
            $table->string('planType', 20);
            $table->integer('idPrice');
            $table->integer('unitPrice');

            $table->index(['contractPlanId']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mContractPlan');
    }
}
