<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMCompanyTable20210916 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mCompany', function (Blueprint $table) {
            $table->string('bank', 50)->default('')->after('fax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mCompany', function (Blueprint $table) {
            $table->dropColumn('bank');
        });
    }
}
