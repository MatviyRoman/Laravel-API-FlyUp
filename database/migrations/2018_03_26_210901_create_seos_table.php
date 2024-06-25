<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('seos', function (Blueprint $table) {
		    $table->increments('id');
		    $table->unsignedTinyInteger('page_id')->length(3)->nullable();
		    $table->unsignedTinyInteger('language_id')->length(3)->nullable();
		    $table->string('title')->nullable();
		    $table->string('seo_title')->nullable();
		    $table->text('keywords')->nullable();
		    $table->text('description')->nullable();
		    $table->string('url')->nullable();
		    $table->timestamps();
	    });

	    Schema::table('seos', function (Blueprint $table) {
		    $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
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
        Schema::dropIfExists('seos');
    }
}
