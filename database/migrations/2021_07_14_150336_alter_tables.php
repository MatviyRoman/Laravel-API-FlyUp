<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('link')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('admin_id')->unsigned()->nullable()->after('user_id');
            $table->foreign('admin_id')->references('id')->on('users');

            $table->string('type', 20)->nullable()->after('status')->default('order');
            $table->json('data')->nullable()->after('phone');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('type', 20)->nullable();
            $table->date('dob')->nullable();

            $table->json('files')->nullable();
            $table->json('users')->nullable();
            $table->json('branches')->nullable();
            $table->json('data')->nullable();
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
