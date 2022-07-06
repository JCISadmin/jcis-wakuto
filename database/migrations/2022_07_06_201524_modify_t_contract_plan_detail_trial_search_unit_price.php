<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTContractPlanDetailTrialSearchUnitPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tContractPlan', function (Blueprint $table) {
            $table->integer('trialSearchUnitPrice')->after('deposit')->nullable();
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
            $table->dropColumn('trialSearchUnitPrice');
        });
    }
}
