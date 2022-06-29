<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MCorporationAddUniCase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mCorporation', function (Blueprint $table) {
            $table->string('uniCaseName',160)->after('dispName');
            $table->index(['uniCaseName']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mCorporation', function (Blueprint $table) {
            $table->dropColumn('uniCaseName');
        });
    }
}
