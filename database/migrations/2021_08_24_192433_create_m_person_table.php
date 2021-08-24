<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 個人情報
 */
class CreateMPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mPerson', function (Blueprint $table) {
            $table->increments('personId');
            $table->string('inputName', 20);
            $table->string('dispName', 20);
            $table->string('inputKana', 20)->nullable();
            $table->string('dispKana', 20)->nullable();
            $table->date('birthday')->nullable();
            $table->string('postCode', 7)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('requireDivision', 50)->nullable();
            $table->string('departmentJob', 20)->nullable();
            $table->string('department', 20)->nullable();
            $table->string('departmentAddress', 200)->nullable();
            $table->date('caseDate')->nullable();
            $table->text('caseSummary')->nullable();
            $table->integer('caseAge')->nullable();
            $table->string('disposalOffice', 50)->nullable();
            $table->string('infoKind', 50)->nullable();
            $table->string('infoSource', 50)->nullable();
            $table->string('filename', 50)->nullable();
            $table->date('regDate')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('createDatetime')->nullable();
            $table->dateTime('updateDatetime')->nullable();


            $table->index(['personId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mPerson');
    }
}
