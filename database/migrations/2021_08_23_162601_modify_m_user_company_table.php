<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMUserCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserCompany', function (Blueprint $table) {
            $table->tinyInteger('contractStatus')->after('chargeMail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mUserCompany', function (Blueprint $table) {
            $table->dropColumn('contractStatus');
        });
    }
}
