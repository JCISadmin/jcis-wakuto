<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateT2FactMngTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t2FactMng', function (Blueprint $table) {
            $table->string('companyId', 20);
            $table->string('contractPlanId', 20);
            $table->string('userId', 20);
            $table->string('manageId', 20);
            $table->string('ipAddress', 20);
            $table->string('userAgent', 20);
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->primary(['companyId', 'contractPlanId', 'userId', 'manageId']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t2FactMng');
    }
}
