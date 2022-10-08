<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Kemahasiswaan;
use App\Mahasiswa;
use App\Pembina;
use Illuminate\Support\Facades\DB;

class AkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $kem = new Kemahasiswaan;
        $kem->nip = '12345678910';
        $kem->name = 'mahasiswa';
        $kem->email = 'mahasiswa@gmail.com';
        $kem->alamat = '-';
        $kem->tanggalLahir = '2001-01-28';
        $kem->noKtp = '0';
        $kem->jk = 'L';
        $kem->noTlpn = '081929192';
        $kem->save();
        $last_id = $kem->id;
        User::create([
            'name' => 'kemahasiswaan',
            'users_global' => $last_id,
            'username' => '12345678910',
            'password' => bcrypt('12345678910'),
            'role' => 'kemahasiswaan',
            'email' => 'kemahasiswaan@gmail.com',
        ]);

        $pem = new Pembina;
        $pem->nip = '2222222222';
        $pem->name = 'mahasiswa';
        $pem->email = 'mahasiswa@gmail.com';
        $pem->alamat = '-';
        $pem->tanggalLahir = '2001-01-28';
        $pem->noKtp = '0';
        $pem->jk = 'L';
        $pem->noTlpn = '081929192';
        $pem->save();
        $last_id = $pem->id;
        User::create([
            'name' => 'pembina',
            'users_global' => $last_id,
            'username' => '2222222222',
            'password' => bcrypt('2222222222'),
            'role' => 'pembina',
            'email' => 'pembina@gmail.com',
        ]);

        $mhs = new Mahasiswa;
        $mhs->nim = '1111111111';
        $mhs->name = 'mahasiswa';
        $mhs->email = 'mahasiswa@gmail.com';
        $mhs->alamat = '-';
        $mhs->tanggalLahir = '2001-01-28';
        $mhs->noKtp = '0';
        $mhs->jk = 'L';
        $mhs->noTlpn = '081929192';
        $mhs->save();
        $last_id = $mhs->id;
        User::create([
            'name' => 'mahasiswa',
            'users_global' => $last_id,
            'username' => '1111111111',
            'password' => bcrypt('1111111111'),
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@gmail.com',
        ]);
    }
}
