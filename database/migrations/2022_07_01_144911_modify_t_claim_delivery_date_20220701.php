<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTClaimDeliveryDate20220701 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tClaim', function (Blueprint $table) {
            $table->string('deliveryDate', 20)->nullable()->after('paymentDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tClaim', function (Blueprint $table) {
            $table->dropColumn('deliveryDate');
        });
    }
}
