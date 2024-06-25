<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeInterface extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interface_translates', function (Blueprint $table) {
            $table->dropForeign('interface_translates_interface_entity_id_foreign');
        });
        Schema::table('interface_translates', function (Blueprint $table) {
            $table->unsignedSmallInteger('interface_entity_id')->nullable()->change();
        });
        Schema::table('interface_entities', function (Blueprint $table) {
            $table->smallIncrements('id')->change();
        });
        Schema::table('interface_translates', function (Blueprint $table) {
            $table->foreign('interface_entity_id')->references('id')->on('interface_entities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
