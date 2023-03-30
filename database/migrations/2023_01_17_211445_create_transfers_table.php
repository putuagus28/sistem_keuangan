<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode')->nullable();
            $table->string("nominal")->default(0);
            $table->uuid('akun_dari')->nullable()->index();
            $table->foreign('akun_dari')->references('id')->on('akuns')->onDelete('cascade');
            $table->uuid('akun_tujuan')->nullable()->index();
            $table->foreign('akun_tujuan')->references('id')->on('akuns')->onDelete('cascade');
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
        Schema::dropIfExists('transfers');
    }
}
