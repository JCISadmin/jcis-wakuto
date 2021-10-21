<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTClaimTable20211020 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tClaim', function (Blueprint $table) {
            $table->tinyInteger('webPrepaidStatus')->nullable()->default(0)->after('updateDatetime');
            $table->tinyInteger('apiPrepaidStatus')->nullable()->default(0)->after('webPrepaidStatus');
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
            $table->dropColumn('webPrepaidStatus');
            $table->dropColumn('apiPrepaidStatus');
        });
    }
}
