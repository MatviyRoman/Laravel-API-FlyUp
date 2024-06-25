<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleCategoryTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_category_translates', function (Blueprint $table) {
	        $table->increments('id');
	        $table->unsignedTinyInteger('article_category_id')->length(3)->nullable();
	        $table->unsignedTinyInteger('language_id')->length(3)->nullable();
	        $table->string('title')->nullable();
	        $table->string('seo_title')->nullable();
	        $table->text('keywords')->nullable();
	        $table->text('description')->nullable();
	        $table->string('url')->nullable();
	        $table->timestamps();
        });

	    Schema::table('article_category_translates', function (Blueprint $table) {
		    $table->foreign('article_category_id')->references('id')->on('article_categories')->onDelete('cascade');
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
        Schema::dropIfExists('article_category_translates');
    }
}
