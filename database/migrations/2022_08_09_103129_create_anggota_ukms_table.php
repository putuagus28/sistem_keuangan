<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnggotaUkmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anggota_ukms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('jabatan', ['ketua', 'bendahara', 'anggota_biasa', 'pembina']);
            $table->uuid('ukms_id')->nullable()->index();
            $table->foreign('ukms_id')->references('id')->on('ukms')->onDelete('cascade');
            $table->uuid('users_global')->nullable()->index();
            // $table->uuid('mahasiswas_id')->nullable()->index();
            // $table->foreign('mahasiswas_id')->references('id')->on('mahasiswas')->onDelete('cascade');
            $table->uuid('users_id')->nullable()->index();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('anggota_ukms');
    }
}
