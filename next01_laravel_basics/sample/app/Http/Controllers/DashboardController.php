<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * ダッシュボード表示
     */
    public function index(): View
    {
        $user = auth()->user();
        
        // 統計情報を取得
        $stats = $user->getTaskStats();
        $completionRate = $user->getCompletionRate();
        
        // 最近のタスクを取得
        $recentTasks = $user->tasks()
                           ->latest()
                           ->limit(5)
                           ->get();
        
        // 期限切れタスクを取得
        $overdueTasks = $user->overdueTasks()
                            ->orderBy('due_date')
                            ->get();
        
        // 今日期限のタスクを取得
        $todayTasks = $user->tasks()
                          ->whereDate('due_date', today())
                          ->incomplete()
                          ->get();
        
        // 今週期限のタスクを取得
        $thisWeekTasks = $user->tasks()
                             ->whereBetween('due_date', [
                                 now()->startOfWeek(),
                                 now()->endOfWeek()
                             ])
                             ->incomplete()
                             ->orderBy('due_date')
                             ->get();

        return view('dashboard', compact(
            'stats',
            'completionRate',
            'recentTasks',
            'overdueTasks',
            'todayTasks',
            'thisWeekTasks'
        ));
    }

    /**
     * API用の統計データ取得
     */
    public function getStats(Request $request)
    {
        $user = auth()->user();
        $stats = $user->getTaskStats();
        $completionRate = $user->getCompletionRate();

        return response()->json([
            'stats' => $stats,
            'completion_rate' => $completionRate,
            'total_tasks' => $stats['total'],
        ]);
    }
}