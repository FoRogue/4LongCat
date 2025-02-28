<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;

class DashboardController extends Controller {
    
    /**
     * Получение статистики за текущий и прошлый месяц
     */
    public function statistics(Request $request) {
        try {
            Log::info('Запрос на /dashboard/statistics');

            $currentMonth = Carbon::now()->month;
            $lastMonth = Carbon::now()->subMonth()->month;

            // Статистика за текущий месяц
            $currentMonthStats = Visit::whereMonth('entry_time', $currentMonth)
                ->selectRaw('
                    AVG(EXTRACT(EPOCH FROM entry_time - entry_time::date)) as avg_entry_time, 
                    AVG(EXTRACT(EPOCH FROM exit_time - exit_time::date)) as avg_exit_time')
                ->first();

            $avgEntryTimeMinutes = isset($currentMonthStats->avg_entry_time) ? round($currentMonthStats->avg_entry_time / 60) : 0;
            $avgExitTimeMinutes = isset($currentMonthStats->avg_exit_time) ? round($currentMonthStats->avg_exit_time / 60) : 0;

            $uniqueVisitorsCurrentMonth = Visitor::whereHas('visits', function ($query) use ($currentMonth) {
                $query->whereMonth('entry_time', $currentMonth);
            })->distinct()->count();

            $notesCurrentMonth = Visit::whereMonth('entry_time', $currentMonth)->whereNotNull('note')->count();

            // Статистика за прошлый месяц
            $lastMonthStats = Visit::whereMonth('entry_time', $lastMonth)
                ->selectRaw('
                    AVG(EXTRACT(EPOCH FROM entry_time - entry_time::date)) as prev_avg_entry_time, 
                    AVG(EXTRACT(EPOCH FROM exit_time - exit_time::date)) as prev_avg_exit_time')
                ->first();

            $prevAvgEntryTimeMinutes = isset($lastMonthStats->prev_avg_entry_time) ? round($lastMonthStats->prev_avg_entry_time / 60) : 0;
            $prevAvgExitTimeMinutes = isset($lastMonthStats->prev_avg_exit_time) ? round($lastMonthStats->prev_avg_exit_time / 60) : 0;

            $uniqueVisitorsLastMonth = Visitor::whereHas('visits', function ($query) use ($lastMonth) {
                $query->whereMonth('entry_time', $lastMonth);
            })->distinct()->count();

            $notesLastMonth = Visit::whereMonth('entry_time', $lastMonth)->whereNotNull('note')->count();

            $response = [
                'avg_entry_time' => $avgEntryTimeMinutes,
                'avg_exit_time' => $avgExitTimeMinutes,
                'unique_visitors' => $uniqueVisitorsCurrentMonth,
                'notes_count' => $notesCurrentMonth,
                'prev_avg_entry_time' => $prevAvgEntryTimeMinutes,
                'prev_avg_exit_time' => $prevAvgExitTimeMinutes,
                'prev_unique_visitors' => $uniqueVisitorsLastMonth,
                'prev_notes_count' => $notesLastMonth
            ];

            Log::info('Статистика успешно собрана', ['data' => $response]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Ошибка в statistics', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Ошибка загрузки статистики'], 500);
        }
    }

    /**
     * Получение лучших и худших сотрудников по времени входа и количеству замечаний
     */
    public function bestPerformers(Request $request) {
        try {
            Log::info('Запрос на /dashboard/best-performers');
    
            $performers = Visitor::select('visitors.*')
                ->withCount('visits as visits_count')
                ->selectRaw('
                    (SELECT AVG(EXTRACT(EPOCH FROM entry_time - DATE_TRUNC(\'day\', entry_time))) / 60 
                     FROM visits WHERE visits.visitor_id = visitors.id) as avg_entry_time,
                    (SELECT COUNT(note) FROM visits WHERE visits.visitor_id = visitors.id AND note IS NOT NULL) as notes_count
                ')
                ->orderBy('avg_entry_time', 'asc')
                ->limit(5)
                ->get();
    
            if ($performers->isEmpty()) {
                return response()->json(["message" => "Нет данных"], 200);
            }
    
            $bestPerformer = $performers->first();
            $worstPerformer = $performers->last();
    
            Log::info('Лучшие и худшие сотрудники получены', ['best' => $bestPerformer, 'worst' => $worstPerformer]);

            return response()->json([
                'best_performer' => $bestPerformer,
                'worst_performer' => $worstPerformer
            ]);
    
        } catch (\Exception $e) {
            Log::error('Ошибка в bestPerformers', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Ошибка загрузки данных'], 500);
        }
    }

    /**
     * Получение данных для графиков (вход, выход, замечания)
     */
    public function graphData(Request $request) {
        try {
            Log::info('Запрос на /dashboard/graph', ['query' => $request->all()]);
    
            $dataType = $request->get('data_type', 'entry_exit');
            $startDate = Carbon::now()->subMonth();
            $endDate = Carbon::now();
    
            Log::info("Запрашиваемые данные: $dataType | Период: $startDate - $endDate");
    
            if ($dataType === 'entry_exit') {
                $data = Visit::whereBetween('entry_time', [$startDate, $endDate])
                    ->selectRaw("DATE_TRUNC('day', entry_time) as date, 
                                AVG(EXTRACT(HOUR FROM entry_time) * 60 + EXTRACT(MINUTE FROM entry_time)) as avg_entry_time")
                    ->groupByRaw("DATE_TRUNC('day', entry_time)")
                    ->orderByRaw("DATE_TRUNC('day', entry_time) ASC")
                    ->get();
    
                $formattedData = [
                    'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray(),
                    'values' => $data->pluck('avg_entry_time')->map(fn($value) => (int) $value)->toArray(),
                ];
            } elseif ($dataType === 'notes') {
                $data = Visit::whereBetween('entry_time', [$startDate, $endDate])
                    ->selectRaw("DATE_TRUNC('day', entry_time) as date, COUNT(note) as notes_count")
                    ->whereNotNull('note')
                    ->groupByRaw("DATE_TRUNC('day', entry_time)")
                    ->orderByRaw("DATE_TRUNC('day', entry_time) ASC")
                    ->get();
    
                $formattedData = [
                    'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray(),
                    'values' => $data->pluck('notes_count')->map(fn($value) => (int) $value)->toArray(),
                ];
            } elseif ($dataType === 'visitors') { // Новый блок для графика с количеством уникальных посетителей
                $data = Visit::whereBetween('entry_time', [$startDate, $endDate])
                    ->selectRaw("DATE_TRUNC('day', entry_time) as date, 
                                COUNT(DISTINCT visitor_id) as unique_visitors")
                    ->groupByRaw("DATE_TRUNC('day', entry_time)")
                    ->orderByRaw("DATE_TRUNC('day', entry_time) ASC")
                    ->get();
    
                $formattedData = [
                    'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray(),
                    'values' => $data->pluck('unique_visitors')->map(fn($value) => (int) $value)->toArray(),
                ];
            } else {
                throw new \Exception("Некорректный параметр data_type: $dataType");
            }
    
            Log::info('Данные для графика успешно сформированы', ['data' => $formattedData]);
    
            return response()->json($formattedData);
    
        } catch (\Exception $e) {
            Log::error('Ошибка в graphData', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Ошибка загрузки данных'], 500);
        }
    }
    
}
