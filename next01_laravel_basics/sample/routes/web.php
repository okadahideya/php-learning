<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 公開ページ
Route::get('/', function () {
    return view('welcome');
});

// 認証が必要なルート
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // タスク管理
    Route::resource('tasks', TaskController::class);
    
    // Ajax用API
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
         ->name('tasks.update-status');
});

// 認証ルート（Laravel Breezeまたは手動実装）
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    // ログイン処理の実装
})->name('login.store');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function () {
    // 登録処理の実装
})->name('register.store');

Route::post('/logout', function () {
    // ログアウト処理の実装
})->name('logout');