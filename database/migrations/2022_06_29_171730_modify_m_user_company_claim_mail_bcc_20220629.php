<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMUserCompanyClaimMailBcc20220629 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserCompany', function (Blueprint $table) {
            $table->text('claimMailBcc')->nullable()->after('claimMailCc');
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
            $table->dropColumn('claimMailBcc');
        });
    }
}
