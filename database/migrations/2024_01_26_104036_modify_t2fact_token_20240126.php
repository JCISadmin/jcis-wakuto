<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyT2factToken20240126 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t2FactToken', function (Blueprint $table) {
            $table->string('userId', 20)->nullable()->after('authCode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t2FactToken', function (Blueprint $table) {
            $table->dropColumn('userId');
        });
    }
}
