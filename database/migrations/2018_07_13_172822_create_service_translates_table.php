<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_translates', function (Blueprint $table) {
	        $table->increments('id');
	        $table->integer('service_id')->unsigned()->nullable();
	        $table->unsignedTinyInteger('language_id')->length(3)->nullable();
	        $table->string('title')->nullable();
            $table->string('alt')->nullable();
	        $table->string('seo_title')->nullable();
	        $table->text('keywords')->nullable();
	        $table->text('description')->nullable();
	        $table->string('url')->nullable();
	        $table->text('text')->nullable();
	        $table->text('subtext')->nullable();
	        $table->timestamps();
        });

	    Schema::table('service_translates', function (Blueprint $table) {
		    $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
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
        Schema::dropIfExists('service_translates');
    }
}
