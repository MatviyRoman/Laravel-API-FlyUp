<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
	        $table->increments('id');
	        $table->boolean('is_active')->default(0);
	        $table->unsignedTinyInteger('order')->length(3)->nullable();
	        $table->text('image')->nullable();
	        $table->string('seo_image')->nullable();
	        $table->string('icon')->nullable();
	        $table->text('docs')->nullable();
	        $table->integer('views')->default(0);
	        $table->integer('likes')->default(0);
            $table->unsignedTinyInteger('article_author_id')->length(3)->nullable();
	        $table->timestamps();
        });

        Schema::table('services', function (Blueprint $table) {
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
        Schema::dropIfExists('services');
    }
}
