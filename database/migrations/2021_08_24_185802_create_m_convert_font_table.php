<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 旧字体変換マスタ
 */
class CreateMConvertFontTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mConvertFont', function (Blueprint $table) {
            $table->string('targetCharacter', 1);

            $table->primary(['targetCharacter']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mConvertFont');
    }
}
