<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterfaceGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interface_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->length(50);
            $table->string('title')->length(100);
            $table->unsignedTinyInteger('page_id')->length(7)->nullable();
            $table->timestamps();
        });
        Schema::table('interface_groups', function (Blueprint $table) {
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interface_groups');
    }
}
