<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTContractPlanDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tContractPlanDetail', function (Blueprint $table) {

            $table->string('companyId', 20);
            $table->string('contractPlanId', 20);
            $table->integer('seqNo');
            $table->string('contractTypeId', 20);
            $table->date('contractStartDate')->nullable();
            $table->date('contractEndDate')->nullable();
            $table->integer('idUnitPrice')->nullable();
            $table->integer('searchUnitPrice')->nullable();
            $table->integer('searchCount')->nullable();
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->primary(['companyId', 'contractPlanId', 'seqNo']);
        });

        Schema::table('tContractPlan', function (Blueprint $table) {
            
            $table->string('contractTypeId', 20)->nullable()->default(null)->change();
            $table->integer('idUnitPrice')->nullable()->default(0)->change();
            $table->integer('searchUnitPrice')->nullable()->default(0)->change();
            $table->integer('searchCount')->nullable()->default(0)->change();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tContractPlanDetail');

        Schema::table('tContractPlan', function (Blueprint $table) {
            
            $table->string('contractTypeId', 20)->change();
            $table->integer('idUnitPrice')->nullable()->change();
            $table->integer('searchUnitPrice')->nullable()->change();
            $table->integer('searchCount')->nullable()->change();

        });

    }
}
