<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->double('origin_price', 8, 2)->unsigned()->after('phone')->nullable();
            $table->double('price', 8, 2)->unsigned()->after('origin_price')->nullable();
            $table->string('discount_code', 50)->after('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->removeColumn('origin_price');
            $table->removeColumn('price');
            $table->removeColumn('discount_code');
        });
    }
}
