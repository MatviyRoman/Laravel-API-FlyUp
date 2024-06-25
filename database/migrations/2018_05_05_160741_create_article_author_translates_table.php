<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleAuthorTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_author_translates', function (Blueprint $table) {
	        $table->increments('id');
	        $table->unsignedTinyInteger('article_author_id')->length(3)->nullable();
	        $table->unsignedTinyInteger('language_id')->length(3)->nullable();
	        $table->string('name')->nullable();
	        $table->timestamps();
        });

	    Schema::table('article_author_translates', function (Blueprint $table) {
		    $table->foreign('article_author_id')->references('id')->on('article_authors')->onDelete('cascade');
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
        Schema::dropIfExists('article_author_translates');
    }
}
