<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run() {
        DB::table('users')->truncate(); // Очищаем таблицу перед вставкой

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Админ',
                'login' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'id' => 2,
                'name' => 'Иван Иванов',
                'login' => 'ivan',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        ]);
    }
}
