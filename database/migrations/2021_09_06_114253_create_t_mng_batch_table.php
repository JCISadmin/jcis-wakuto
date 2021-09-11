<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * バッチ管理テーブル
 */
class CreateTMngBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tMngBatch', function (Blueprint $table) {
            $table->string('companyId', 20);
            $table->string('batchId', 20);
            $table->text('searchCondition');
            $table->string('result', 20)->nullable();
            $table->string('errorCode', 20)->nullable();
            $table->string('fileName', 80)->nullable();
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->primary(['companyId', 'batchId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tMngBatch');
    }
}
