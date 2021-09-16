<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMVatTable20210916 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mVat', function (Blueprint $table) {
            $table->date('endDate')->default('3000-12-31')->after('startDate');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mVat', function (Blueprint $table) {
            $table->dropColumn('endDate');
        });

    }
}
