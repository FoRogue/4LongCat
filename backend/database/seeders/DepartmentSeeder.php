<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder {
    public function run() {
        DB::table('departments')->truncate(); // Очищаем таблицу

        DB::table('departments')->insert([
            ['id' => 1, 'name' => 'IT'],
            ['id' => 2, 'name' => 'HR'],
            ['id' => 3, 'name' => 'Охрана'],
            ['id' => 4, 'name' => 'Кадры'],
        ]);
    }
}
