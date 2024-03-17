<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_product', function (Blueprint $table) {
            $table->foreign('branch_id')
                  ->references('branch_id')
                  ->on('m_branch')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_product', function (Blueprint $table) {
            $table->dropForeign('products_branch_id_foreign');
        });
    }
}
