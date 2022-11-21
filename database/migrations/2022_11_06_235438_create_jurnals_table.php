<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('no_reff', 10)->nullable();
            $table->integer("debet")->default(0);
            $table->integer("kredit")->default(0);
            $table->date("tanggal")->nullable();
            $table->char('keterangan', 100)->nullable();

            $table->uuid('id_transaksi')->nullable()->index();
            /** akuns */
            $table->uuid('akuns_id')->nullable()->index();
            $table->foreign('akuns_id')->references('id')->on('akuns')->onDelete('cascade');
            /** field per ukm */
            $table->uuid('ukms_id')->nullable()->index();
            $table->foreign('ukms_id')->references('id')->on('ukms')->onDelete('cascade');
            /** field id user input */
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
        Schema::dropIfExists('jurnals');
    }
}
