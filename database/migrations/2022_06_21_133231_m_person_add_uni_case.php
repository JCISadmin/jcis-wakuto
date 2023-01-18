<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MPersonAddUniCase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mPerson', function (Blueprint $table) {
            $table->string('uniCaseName',260)->after('dispName');
            $table->string('uniCaseKana',260)->after('dispKana')->nullable();
            $table->index(['uniCaseName', 'uniCaseKana']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mPerson', function (Blueprint $table) {
            $table->dropColumn('uniCaseName');
            $table->dropColumn('uniCaseKana');
        });
    }
}
