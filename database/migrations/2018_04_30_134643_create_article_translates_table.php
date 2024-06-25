<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_translates', function (Blueprint $table) {
	        $table->increments('id');
	        $table->integer('article_id')->unsigned()->nullable();
	        $table->unsignedTinyInteger('language_id')->length(3)->nullable();
	        $table->string('title')->nullable();
	        $table->string('seo_title')->nullable();
	        $table->text('keywords')->nullable();
            $table->string('alt')->nullable();
	        $table->text('description')->nullable();
	        $table->string('url')->nullable();
	        $table->text('text')->nullable();
	        $table->text('subtext')->nullable();
	        $table->timestamps();
        });

	    Schema::table('article_translates', function (Blueprint $table) {
		    $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
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
        Schema::dropIfExists('article_translates');
    }
}
