<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TContractPlanModify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tContractPlan', function (Blueprint $table) {
            
            $table->string('contractTypeId', 20)->nullable()->default(0)->change();
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
        Schema::table('tContractPlan', function (Blueprint $table) {
            
            $table->string('contractTypeId', 20)->change();
            $table->integer('idUnitPrice')->nullable()->change();
            $table->integer('searchUnitPrice')->nullable()->change();
            $table->integer('searchCount')->nullable()->change();

        });
    }
}
