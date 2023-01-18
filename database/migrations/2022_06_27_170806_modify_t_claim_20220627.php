<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTClaim20220627 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tClaim', function (Blueprint $table) {
            $table->dropColumn('adjustNote');
            $table->dropColumn('adjustPrice');
            $table->text('claimNote')->nullable();
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
            $table->string('adjustNote',20)->nullable();
            $table->integer('adjustPrice')->nullable();
            $table->dropColumn('claimNote');
        });
    }
}
