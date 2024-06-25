<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(0);
            $table->unsignedTinyInteger('order')->length(3)->nullable();
            $table->timestamps();
        });

//        Schema::table('clients', function (Blueprint $table) {
//            $table->foreign('id')->references('client_id')->on('clients')->onDelete('SET NULL');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
