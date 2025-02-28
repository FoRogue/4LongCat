<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitorSeeder extends Seeder {
    public function run() {
        DB::table('visitors')->truncate(); // Очищаем таблицу перед вставкой

        DB::table('visitors')->insert([
            [
                'id' => 1,
                'user_id' => 2, // Привязываем к пользователю Иван Иванов
                'full_name' => 'Иван Иванов',
                'birth_date' => '1985-06-15',
                'position' => 'Инженер',
                'phone' => '+7(900)123-45-67',
                'department_id' => 1,
                'document_type' => 'passport',
                'document_series' => '1234',
                'document_number' => '567890',
                'document_issue_date' => '2010-05-10',
                'document_issued_by' => 'МВД России',
                'passport_code' => '770-001'
            ],
        ]);
    }
}
