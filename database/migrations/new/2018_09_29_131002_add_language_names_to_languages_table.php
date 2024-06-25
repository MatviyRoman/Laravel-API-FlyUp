<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguageNamesToLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('langueges', function (Blueprint $table) {
            $table->string('native_name')->after('flag');
            $table->string('english_name')->after('native_name');
            $table->renameColumn('name', 'short_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('langueges', function (Blueprint $table) {
            $table->dropColumn('native_name');
            $table->dropColumn('english_name');
            $table->renameColumn('short_name', 'name');
        });
    }
}
