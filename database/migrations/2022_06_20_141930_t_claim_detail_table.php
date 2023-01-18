<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TClaimDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tClaimDetail', function (Blueprint $table) {
            
            $table->string('companyId', 20);
            $table->string('claimMonth', 6);
            $table->integer('seqNo');
            $table->string('type', 20)->nullable();
            $table->boolean('useFlg')->default(false);
            $table->text('itemName')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('unitPrice')->nullable();
            $table->integer('price')->nullable();
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->primary(['companyId', 'claimMonth', 'seqNo']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tClaimDetail');
    }
}
