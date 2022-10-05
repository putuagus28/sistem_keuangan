<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailKegiatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_kegiatans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('tanggal')->nullable();
            $table->char('nominal', 25)->nullable();
            $table->char('status', 25)->nullable();
            $table->string('keterangan')->nullable();
            $table->string('bukti')->nullable();
            $table->uuid('kegiatans_id')->nullable()->index();
            $table->foreign('kegiatans_id')->references('id')->on('kegiatans')->onDelete('cascade');
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
        Schema::dropIfExists('detail_kegiatans');
    }
}
