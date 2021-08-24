<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 旧字体変換マスタ詳細
 */
class CreateMConvertFontDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mConvertFontDetail', function (Blueprint $table) {
            $table->string('targetCharacter', 1);
            $table->string('convertCharacter', 1);

            $table->index(['targetCharacter', 'convertCharacter']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mConvertFontDetail');
    }
}
