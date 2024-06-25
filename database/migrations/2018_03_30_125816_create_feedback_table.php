<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id');
	        $table->string('name')->nullable();
	        $table->string('email')->nullable();
	        $table->text('message')->nullable();
	        $table->text('comment')->nullable();
            $table->string('phone')->nullable();
	        $table->unsignedTinyInteger('language_id')->length(3)->nullable();
	        $table->unsignedTinyInteger('service_id')->length(3)->nullable();
	        $table->string('file')->nullable();
	        $table->string('type')->nullable();
	        $table->boolean('is_viewed')->default(0);
            $table->timestamps();
        });

	    Schema::table('feedback', function (Blueprint $table) {
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
        Schema::dropIfExists('feedback');
    }
}
