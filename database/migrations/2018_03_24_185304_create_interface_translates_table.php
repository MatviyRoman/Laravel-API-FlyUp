<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterfaceTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interface_translates', function (Blueprint $table) {
            $table->increments('id');
	        $table->unsignedTinyInteger('interface_entity_id')->length(5)->nullable();
	        $table->unsignedTinyInteger('language_id')->length(3)->nullable();
	        $table->string('value')->nullable();
            $table->timestamps();
        });

	    Schema::table('interface_translates', function (Blueprint $table) {
		    $table->foreign('interface_entity_id')->references('id')->on('interface_entities')->onDelete('cascade');
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
        Schema::dropIfExists('interface_translates');
    }
}
