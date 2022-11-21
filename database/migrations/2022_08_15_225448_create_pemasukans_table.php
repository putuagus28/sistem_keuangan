<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemasukansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemasukans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode')->nullable();
            $table->date('tanggal')->nullable();
            $table->char('nominal', 25)->nullable();
            $table->char('metode', 25)->nullable();
            $table->string('keterangan')->nullable();
            $table->uuid('ukms_id')->nullable()->index();
            $table->foreign('ukms_id')->references('id')->on('ukms')->onDelete('cascade');
            $table->uuid('users_id')->nullable()->index();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            /** akutansi */
            $table->char('no_reff', 10)->nullable();
            $table->integer("debet")->default(0);
            $table->integer("kredit")->default(0);
            $table->uuid('akuns_id')->nullable()->index();
            $table->foreign('akuns_id')->references('id')->on('akuns')->onDelete('cascade');
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
        Schema::dropIfExists('pemasukans');
    }
}
