<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mAdminUser', function (Blueprint $table) {
            $table->tinyInteger('viewPermissionFlg')->default(0)->after('mail');
            $table->string('agent_cd')->after('delFlg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mAdminUser', function (Blueprint $table) {
            $table->dropColumn('viewPermissionFlg');
            $table->dropColumn('agent_cd');
        });
    }
}
