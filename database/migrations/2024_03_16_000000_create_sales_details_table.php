<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_sales_detail', function (Blueprint $table) {
            $table->increments('sales_detail_id');
            $table->integer('sale_id');
            $table->integer('product_id');
            $table->integer('selling_price');
            $table->integer('quantity');
            $table->tinyInteger('discount')->default(0);
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_sales_detail');
    }
}
