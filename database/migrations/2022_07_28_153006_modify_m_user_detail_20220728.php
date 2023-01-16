<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMUserDetail20220728 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mUserDetail', function (Blueprint $table) {
            $table->text('idMailBcc')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mUserDetail', function (Blueprint $table) {
            $table->string('idMailBcc', 255)->nullable()->change();
        });
    }
}
