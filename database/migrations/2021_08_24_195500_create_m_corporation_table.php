<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 法人情報
 */
class CreateMCorporationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mCorporation', function (Blueprint $table) {
            $table->increments('corporationId');
            $table->string('inputName', 20);
            $table->string('dispName', 20);
            $table->string('industry', 20)->nullable();
            $table->string('postCode', 7)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('corporateCode', 20)->nullable();
            $table->string('tel', 20)->nullable();
            $table->string('requireDivision', 50)->nullable();
            $table->string('businessOwner', 20)->nullable();
            $table->string('department', 20)->nullable();
            $table->string('delegate', 20)->nullable();
            $table->string('casePersonName', 20)->nullable();
            $table->date('caseDate')->nullable();
            $table->text('caseSummary')->nullable();
            $table->string('disposalOffice', 50)->nullable();
            $table->string('infoKind', 50)->nullable();
            $table->string('infoSource', 50)->nullable();
            $table->string('filename', 50)->nullable();
            $table->date('regDate')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();

            $table->index(['inputName']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mCorporation');
    }
}
