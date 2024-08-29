<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mCompany', function (Blueprint $table) {
            $table->string('mailCompanyName', 20)->nullable();
            $table->string('homePageUrl', 100)->nullable();
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
            $table->dropColumn('mailCompanyName');
            $table->dropColumn('homePageUrl');
        });
    }
}
