<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lab_id');
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->unsignedInteger('machine_total')->default(0);
            $table->unsignedInteger('logged_in_total')->default(0);
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
        Schema::dropIfExists('lab_stats');
    }
}
