<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacanciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->boolean('is_active')->default(0);
            $table->unsignedTinyInteger('order')->length(3)->nullable();
            $table->timestamps();
        });

//        Schema::table('vacancies', function (Blueprint $table) {
//            $table->foreign('id')->references('vacancy_id')->on('vacancies')->onDelete('SET NULL');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacancies');
    }
}
