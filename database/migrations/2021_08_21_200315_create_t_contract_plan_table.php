<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 契約プラン
 */
class CreateTContractPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tContractPlan', function (Blueprint $table) {

            $table->string('companyId', 20);
            $table->string('contractPlanId', 20);
            $table->string('contractTypeId', 20);
            $table->date('startTrial')->nullable();
            $table->date('useStartDate')->nullable();
            $table->date('useUpdateDate')->nullable();
            $table->date('useEndAlertDate')->nullable();
            $table->date('useEndDate')->nullable();
            $table->integer('idUnitPrice')->nullable();
            $table->integer('searchUnitPrice')->nullable();
            $table->integer('searchCount')->nullable();
            $table->integer('deposit')->nullable();
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->index(['companyId', 'contractPlanId']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tContractPlan');
    }
}
