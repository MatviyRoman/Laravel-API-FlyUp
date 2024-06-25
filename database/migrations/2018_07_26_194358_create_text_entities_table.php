<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_entities', function (Blueprint $table) {
	        $table->tinyIncrements('id')->length(5);
	        $table->unsignedInteger('interface_group_id')->nullable();
            $table->string('title')->length(100);
	        $table->string('name')->length(100);
            $table->timestamps();
        });

	    Schema::table('text_entities', function (Blueprint $table) {
		    $table->foreign('interface_group_id')->references('id')->on('interface_groups')->onDelete('set null');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('text_entities');
    }
}
