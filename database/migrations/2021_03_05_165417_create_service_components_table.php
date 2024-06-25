<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_components', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id')->unsigned()->nullable();
            $table->foreign('service_id')->references('id')->on('services');

            $table->string('name')->nullable();
            $table->string('notes')->nullable();
            $table->string('image')->nullable();
            $table->string('reg_number')->nullable();
            $table->double('price', 8, 2)->unsigned()->nullable();
            $table->dateTime('work_start')->nullable();
            $table->dateTime('work_end')->nullable();
            $table->text('repair')->nullable();
            $table->text('inspection')->nullable();

            $table->unsignedInteger('count')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_components');
    }
}
