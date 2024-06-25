<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_translates', function (Blueprint $table) {
            $table->increments('id');
	        $table->unsignedTinyInteger('text_entity_id')->length(5)->nullable();
	        $table->unsignedTinyInteger('language_id')->length(3)->nullable();
	        $table->text('value')->nullable();
            $table->timestamps();
        });

	    Schema::table('text_translates', function (Blueprint $table) {
		    $table->foreign('text_entity_id')->references('id')->on('text_entities')->onDelete('cascade');
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
        Schema::dropIfExists('text_translates');
    }
}
