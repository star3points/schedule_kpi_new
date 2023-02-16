<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthShopDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('month_shop_data', function (Blueprint $table) {
            $table->id();
            $table->string('shop_bx_id');
            $table->date('month');
            $table->text('comments');
            $table->integer('sales_plan');
            $table->integer('qty_workers');
            $table->boolean('month_closed');
            $table->timestamps();
            $table->unique(['shop_bx_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('month_shop_data');
    }
}
