<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMAdminUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // hashパスワードカラム追加
        Schema::table('mAdminUser', function (Blueprint $table) {
            $table->string('password_hash',20)->nullable()->comment('パスワード(hash)')->after('password');
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
            $table->dropColumn('password_hash');
        });
    }
}
