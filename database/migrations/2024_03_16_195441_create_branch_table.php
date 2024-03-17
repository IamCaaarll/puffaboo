<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_branch', function (Blueprint $table) {
            $table->increments('branch_id');
            $table->string('branch_name')->unique();
            $table->string('phone')->unique();
            $table->text('address')->nullable();
            $table->string('active')->unique();
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
        Schema::dropIfExists('m_branch');
    }
}
