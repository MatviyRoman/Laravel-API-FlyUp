<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->tinyIncrements('id');
	        $table->unsignedTinyInteger('article_category_id')->length(3)->nullable();
	        $table->boolean('is_active')->default(0);
	        $table->boolean('is_last')->default(1);
	        $table->boolean('has_articles')->default(0);
	        $table->unsignedTinyInteger('order')->length(3)->nullable();
	        $table->string('image')->nullable();
	        $table->string('seo_image')->nullable();
	        $table->string('icon')->nullable();
	        $table->integer('likes')->default(0);
            $table->timestamps();
        });

	    Schema::table('article_categories', function (Blueprint $table) {
		    $table->foreign('article_category_id')->references('id')->on('article_categories')->onDelete('set null');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_categories');
    }
}
