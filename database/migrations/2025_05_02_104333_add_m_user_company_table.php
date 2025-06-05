<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMUserCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // hashパスワードカラム追加
        Schema::table('mUserCompany', function (Blueprint $table) {
            $table->string('distributor_cd',20)->nullable()->comment('販売店CD')->after('companyId');
            $table->string('agent_cd',20)->nullable()->comment('代理店CD')->after('distributor_cd');
            $table->integer('pref_code')->length(3)->nullable()->comment('都道府県コード')->after('postCode');
            $table->string('president',20)->comment('会社代表')->nullable()->after('tel');
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
            $table->dropColumn('distributor_cd');
            $table->dropColumn('agent_cd');
            $table->dropColumn('pref_code');
            $table->dropColumn('president');
        });
    }
}
