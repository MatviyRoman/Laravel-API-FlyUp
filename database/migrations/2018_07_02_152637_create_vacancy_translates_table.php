<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacancyTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacancy_translates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('vacancy_id')->length(3)->nullable();
            $table->unsignedTinyInteger('language_id')->length(3)->nullable();
            $table->string('name')->nullable();
            $table->text('text')->nullable();
            $table->timestamps();
        });

        Schema::table('vacancy_translates', function (Blueprint $table) {
            $table->foreign('vacancy_id')->references('id')->on('vacancies')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacancy_translates');
    }
}
