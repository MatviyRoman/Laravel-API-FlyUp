<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateServiceUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_units', function (Blueprint $table) {
            $table->string('image')->after('notes')->nullable();
            $table->string('number')->after('image')->nullable();
            $table->double('price', 8, 2)->unsigned()->after('number')->nullable();
            $table->string('status', 10)->after('price')->nullable();
            $table->dateTime('work_start')->after('status')->nullable();
            $table->dateTime('work_end')->after('work_start')->nullable();

            $table->text('repair')->after('work_end')->nullable();
//            $table->dateTime('repair_date')->after('repair')->nullable();
//            $table->double('repair_price', 8, 2)->unsigned()->after('repair_date')->nullable();

            $table->text('inspection')->after('repair')->nullable();
//            $table->dateTime('inspection_date')->after('inspection')->nullable();
//            $table->double('inspection_price', 8, 2)->unsigned()->after('inspection_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_units', function (Blueprint $table) {
//            $table->dropColumn('inspection_price');
//            $table->dropColumn('inspection_date');
            $table->dropColumn('inspection');

//            $table->dropColumn('repair_price');
//            $table->dropColumn('repair_date');
            $table->dropColumn('repair');

            $table->dropColumn('work_end');
            $table->dropColumn('work_start');
            $table->dropColumn('status');
            $table->dropColumn('price');
            $table->dropColumn('number');
            $table->dropColumn('image');
        });
    }
}
