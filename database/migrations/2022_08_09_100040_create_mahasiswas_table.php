<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMahasiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('nim', 25)->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('jk', ['P', 'L']);
            $table->string('alamat')->nullable();
            $table->date('tanggalLahir')->nullable();
            $table->string('noKtp')->nullable();
            $table->string('foto')->nullable();
            $table->string('noTlpn')->nullable();
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
        Schema::dropIfExists('mahasiswas');
    }
}
