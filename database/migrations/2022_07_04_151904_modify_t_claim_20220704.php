<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTClaim20220704 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tClaim', function (Blueprint $table) {
            $table->string('name', 20)->nullable()->after('adjustPrice');
            $table->string('postCode', 7)->nullable()->after('name');
            $table->string('address', 200)->nullable()->after('postCode');
            $table->string('tel', 20)->nullable()->after('address');
            $table->string('chargeName', 20)->nullable()->after('tel');
            $table->string('chargeMail', 255)->nullable()->after('chargeName');
            $table->string('claimName', 20)->nullable()->after('chargeMail');
            $table->string('claimDepartmentJob', 20)->nullable()->after('claimName');
            $table->string('claimTel', 20)->nullable()->after('claimDepartmentJob');
            $table->text('claimMailTo')->nullable()->after('claimTel');
            $table->text('claimMailCc')->nullable()->after('claimMailTo');
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
        Schema::table('tClaim', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('postCode');
            $table->dropColumn('address');
            $table->dropColumn('tel');
            $table->dropColumn('chargeName');
            $table->dropColumn('chargeMail');
            $table->dropColumn('claimName');
            $table->dropColumn('claimDepartmentJob');
            $table->dropColumn('claimTel');
            $table->dropColumn('claimMailTo');
            $table->dropColumn('claimMailCc');
            $table->dropColumn('claimMailBcc');
        });
    }
}
