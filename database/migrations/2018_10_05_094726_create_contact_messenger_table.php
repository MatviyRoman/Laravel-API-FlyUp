<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactMessengerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_messenger', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('contact_id')->length(3)->nullable();
            $table->unsignedTinyInteger('messenger_id')->length(3)->nullable();
            $table->timestamps();
        });

        Schema::table('contact_messenger', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('messenger_id')->references('id')->on('messengers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_messenger');
    }
}
