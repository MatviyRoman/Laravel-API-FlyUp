<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropSoftDeletes();
            $table->integer('user_id')->unsigned()->after('id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('zip', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('city');
            $table->dropColumn('street');
            $table->dropColumn('zip');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
