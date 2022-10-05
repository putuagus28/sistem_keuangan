<?php

use Illuminate\Database\Seeder;
use App\User;
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
        User::create([
            'name' => 'kemahasiswaan',
            'username' => 'kemahasiswaan',
            'password' => bcrypt('kemahasiswaan'),
            'role' => 'kemahasiswaan',
            'email' => 'kemahasiswaan@gmail.com',
        ]);
        User::create([
            'name' => 'pembina',
            'username' => 'pembina',
            'password' => bcrypt('pembina'),
            'role' => 'pembina',
            'email' => 'pembina@gmail.com',
        ]);
        User::create([
            'name' => 'mahasiswa',
            'username' => '11111',
            'password' => bcrypt('11111'),
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@gmail.com',
        ]);
    }
}
