<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 契約形態マスタ
 */
class CreateMContractTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mContractType', function (Blueprint $table) {

            $table->string('contractTypeId', 20);
            $table->string('name', 20);

            $table->index(['contractTypeId']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mContractType');
    }
}
