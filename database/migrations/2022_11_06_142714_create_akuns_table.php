<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAkunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('akuns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('no_reff', 10)->nullable();
            $table->char('nama_reff', 100)->nullable();
            $table->char('keterangan', 100)->nullable();
            /** field per ukm */
            $table->uuid('ukms_id')->nullable()->index();
            $table->foreign('ukms_id')->references('id')->on('ukms')->onDelete('cascade');
            /** field id user input */
            $table->uuid('users_id')->nullable()->index();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer("saldo_awal")->default(0);
            $table->integer("debet")->default(0);
            $table->integer("kredit")->default(0);
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
        Schema::dropIfExists('akuns');
    }
}
