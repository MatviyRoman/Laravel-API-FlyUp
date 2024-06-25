<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_translates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('client_id')->length(3)->nullable();
            $table->unsignedTinyInteger('language_id')->length(3)->nullable();
            $table->string('name')->nullable();
            $table->string('alt')->nullable();
            $table->timestamps();
        });
        Schema::table('client_translates', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
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
        Schema::dropIfExists('client_translates');
    }
}
