<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->double('price', 8, 2)->unsigned()->after('icon')->nullable();
            $table->double('price2', 8, 2)->unsigned()->after('price')->nullable();
            $table->double('price3', 8, 2)->unsigned()->after('price2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->removeColumn('price3');
            $table->removeColumn('price2');
            $table->removeColumn('price');
        });
    }
}
