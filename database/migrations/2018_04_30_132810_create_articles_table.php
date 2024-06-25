<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
	        $table->increments('id');
	        $table->unsignedTinyInteger('article_category_id')->length(3)->nullable();
	        $table->unsignedTinyInteger('article_author_id')->length(3)->nullable();
	        $table->boolean('is_active')->default(0);
	        $table->unsignedTinyInteger('order')->length(3)->nullable();
	        $table->string('image')->nullable();
	        $table->string('seo_image')->nullable();
	        $table->integer('views')->default(0);
	        $table->integer('likes')->default(0);
	        $table->timestamps();
        });

	    Schema::table('articles', function (Blueprint $table) {
		    $table->foreign('article_category_id')->references('id')->on('article_categories')->onDelete('set null');
		    $table->foreign('article_author_id')->references('id')->on('article_authors')->onDelete('set null');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
