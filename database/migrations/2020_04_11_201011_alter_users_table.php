<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('contact_person_name')->nullable()->after('email');
            $table->string('contact_person_email')->nullable()->after('email');
            $table->string('contact_person_phone')->nullable()->after('email');
            $table->string('phone')->length(20)->nullable()->after('email');
            $table->string('company_name')->nullable()->after('email');
            $table->string('ytunnus', 20)->nullable()->after('email');
            $table->string('zip')->length(10)->nullable()->after('email');
            $table->string('image')->nullable()->after('email');
            $table->text('address')->nullable()->after('email');
            $table->string('gender', 10)->nullable()->after('email');
            $table->unsignedTinyInteger('language_id')->length(3)->nullable()->after('email');
            $table->foreign('language_id')->references('id')->on('languages');
            $table->string('last_name')->length(50)->nullable()->after('email');
            $table->string('first_name')->length(50)->nullable()->after('email');
            $table->string('verification_token', 100)->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verification_token');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropForeign(['language_id']);
            $table->dropColumn('language_id');
            $table->dropColumn('gender');
            $table->dropColumn('address');
            $table->dropColumn('image');
            $table->dropColumn('zip');
            $table->dropColumn('ytunnus');
            $table->dropColumn('company_name');
            $table->dropColumn('phone');
            $table->dropColumn('contact_person_phone');
            $table->dropColumn('contact_person_email');
            $table->dropColumn('contact_person_name');
        });
    }
}
