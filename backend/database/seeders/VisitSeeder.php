<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitSeeder extends Seeder
{
    public function run()
    {
        $visitors = [1, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21]; // IDs посетителей
        $statuses = ["", "Опоздание", "Ранний выход", "Проблемы с доступом", "Поздний вход", "Оставался после работы"]; // Статусы

        $startDate = Carbon::create(2025, 1, 1, 0, 0, 0); // Начало периода (1 января 2025 года)
        $endDate = Carbon::create(2025, 3, 31, 23, 59, 59); // Конец периода (31 марта 2025 года)

        $currentDate = $startDate;

        while ($currentDate <= $endDate) {
            // Генерация 3-5 случайных посещений в день
            $numVisits = rand(3, 5);

            for ($i = 0; $i < $numVisits; $i++) {
                $visitorId = $visitors[array_rand($visitors)];
                $status = $statuses[array_rand($statuses)];

                DB::table('visits')->insert([
                    'visitor_id' => $visitorId,
                    'entry_time' => $currentDate->copy()->setTime(rand(8, 10), rand(0, 59)),
                    'exit_time' => $currentDate->copy()->setTime(rand(17, 18), rand(0, 59)),
                    'note' => $status,
                ]);
            }

            // Переход к следующему дню
            $currentDate->addDay();
        }
    }
}
