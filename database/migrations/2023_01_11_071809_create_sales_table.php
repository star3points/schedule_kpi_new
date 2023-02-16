<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('shop_bx_id');
            $table->string('user_bx_id');
            $table->string('shop_role');
            $table->integer('sales_checks');
            $table->integer('sales_products');
            $table->double('sales_sum', 16, 2);
            $table->integer('return_checks');
            $table->integer('return_products');
            $table->double('return_sum', 16, 2);
            $table->timestamps();
            $table->unique(['date', 'shop_bx_id', 'user_bx_id', 'shop_role']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
