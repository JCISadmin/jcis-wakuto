<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 会社マスタ
 */
class CreateMCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mCompany', function (Blueprint $table) {
            $table->string('id', 20);
            $table->string('name', 20);
            $table->string('postCode', 7);
            $table->string('address', 200);
            $table->string('tel', 20);
            $table->string('fax', 20);

            $table->index(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mCompany');
    }
}
