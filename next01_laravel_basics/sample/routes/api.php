<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 認証が必要なAPIルート
Route::middleware('auth:sanctum')->group(function () {
    
    // ユーザー情報取得
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // ダッシュボード統計API
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    
    // タスクAPI
    Route::apiResource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    
    // タスク一覧（フィルタリング付き）
    Route::get('/tasks-filtered', function (Request $request) {
        $user = $request->user();
        $query = $user->tasks();
        
        // ステータスフィルタ
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // 優先度フィルタ
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // 期限フィルタ
        if ($request->has('overdue') && $request->overdue) {
            $query->where('due_date', '<', now())
                  ->where('status', '!=', 'completed');
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    });
});