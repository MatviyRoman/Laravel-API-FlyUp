<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_language', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('contact_id')->length(3)->nullable();
            $table->unsignedTinyInteger('language_id')->length(3)->nullable();
            $table->timestamps();
        });

        Schema::table('contact_language', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
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
        Schema::dropIfExists('contact_language');
    }
}
