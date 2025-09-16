# Laravel基礎学習サンプル - TaskManagerアプリ

Laravel基礎学習のためのシンプルなタスク管理システムです。

## 📋 概要

このサンプルアプリケーションでは、以下のLaravel基礎機能を学習できます：

- **MVC アーキテクチャ**
- **Eloquent ORM**
- **Blade テンプレート**
- **ルーティング**
- **マイグレーション**
- **ポリシー（認可）**
- **フォームバリデーション**

## 🚀 セットアップ方法

### 1. 依存関係インストール
```bash
composer install
```

### 2. 環境設定
```bash
cp .env.example .env
php artisan key:generate
```

### 3. データベースセットアップ
```bash
# SQLiteファイル作成
touch database/database.sqlite

# マイグレーション実行
php artisan migrate
```

### 4. サーバー起動
```bash
php artisan serve
```

ブラウザで `http://localhost:8000` にアクセス

## 📁 ファイル構成

```
sample/
├── app/
│   ├── Http/Controllers/
│   │   ├── TaskController.php          # タスクCRUD
│   │   └── DashboardController.php     # ダッシュボード
│   ├── Models/
│   │   ├── Task.php                    # タスクモデル
│   │   └── User.php                    # ユーザーモデル
│   └── Policies/
│       └── TaskPolicy.php              # タスク認可
├── database/migrations/
│   ├── create_users_table.php          # ユーザーテーブル
│   └── create_tasks_table.php          # タスクテーブル
├── resources/views/
│   ├── layouts/app.blade.php           # 共通レイアウト
│   ├── dashboard.blade.php             # ダッシュボード
│   └── tasks/                          # タスク関連ビュー
│       ├── index.blade.php             # 一覧（カンバン）
│       ├── create.blade.php            # 作成フォーム
│       ├── show.blade.php              # 詳細表示
│       ├── edit.blade.php              # 編集フォーム
│       └── partials/
│           └── task-card.blade.php     # タスクカード
└── routes/
    ├── web.php                         # Webルート
    └── api.php                         # APIルート
```

## 🎯 主要機能

### 1. ダッシュボード
- タスクの統計情報表示
- 期限切れタスクのアラート
- 今日期限・今週期限のタスク表示
- 最近のタスク一覧

### 2. タスク管理
- **CRUD操作**: 作成・読み取り・更新・削除
- **カンバンボード**: ステータス別表示（TODO / 進行中 / 完了）
- **優先度設定**: 高・中・低
- **期限管理**: 期限切れの自動検出
- **Ajax更新**: ページリロードなしでステータス変更

### 3. セキュリティ
- **ポリシーベース認可**: 自分のタスクのみ操作可能
- **CSRFプロテクション**: フォーム送信時の保護
- **バリデーション**: 入力値の検証

## 🔍 学習ポイント

### MVCアーキテクチャ
```php
// Model: app/Models/Task.php
class Task extends Model
{
    // リレーション定義
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

// Controller: app/Http/Controllers/TaskController.php
class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = auth()->user()->tasks;
        return view('tasks.index', compact('tasks'));
    }
}

// View: resources/views/tasks/index.blade.php
@extends('layouts.app')
@section('content')
    <!-- Bladeテンプレート -->
@endsection
```

### Eloquentリレーション
```php
// User.php
public function tasks(): HasMany
{
    return $this->hasMany(Task::class);
}

// Task.php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

// 使用例
$user = auth()->user();
$userTasks = $user->tasks; // ユーザーのタスク一覧
$task = Task::find(1);
$taskOwner = $task->user; // タスクの所有者
```

### Bladeテンプレート
```php
// レイアウト継承
@extends('layouts.app')

// セクション定義
@section('content')
    <h1>{{ $task->title }}</h1>
@endsection

// 条件分岐
@if($task->isOverdue())
    <span class="text-red-500">期限切れ</span>
@endif

// ループ
@foreach($tasks as $task)
    @include('tasks.partials.task-card', ['task' => $task])
@endforeach
```

### フォームバリデーション
```php
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'priority' => 'required|in:low,medium,high',
        'due_date' => 'nullable|date|after:today',
    ]);

    auth()->user()->tasks()->create($validated);

    return redirect()->route('tasks.index')
                     ->with('success', 'タスクを作成しました。');
}
```

### ポリシー（認可）
```php
// TaskPolicy.php
public function view(User $user, Task $task): bool
{
    return $task->user_id === $user->id;
}

// Controller内での使用
public function show(Task $task): View
{
    $this->authorize('view', $task);
    return view('tasks.show', compact('task'));
}
```

## 🔧 カスタマイズ例

### 1. カテゴリ機能追加
```php
// Migration
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('color')->default('#3B82F6');
    $table->timestamps();
});

// Taskテーブルにcategory_id追加
Schema::table('tasks', function (Blueprint $table) {
    $table->foreignId('category_id')->nullable()->constrained();
});
```

### 2. コメント機能追加
```php
// Migration
Schema::create('task_comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('task_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained();
    $table->text('comment');
    $table->timestamps();
});
```

### 3. 添付ファイル機能
```php
// Migration
Schema::create('task_attachments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('task_id')->constrained()->onDelete('cascade');
    $table->string('filename');
    $table->string('path');
    $table->integer('size');
    $table->timestamps();
});
```

## 🚀 発展学習

このサンプルをベースに以下の機能を追加して学習を深められます：

1. **認証機能**: Laravel Breezeの導入
2. **API開発**: RESTful APIエンドポイント
3. **テスト**: PHPUnitでのテスト作成
4. **通知機能**: メール・ブラウザ通知
5. **検索機能**: Elasticsearchとの連携
6. **リアルタイム**: WebSocketでのリアルタイム更新

## 📚 参考資料

- [Laravel公式ドキュメント](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Blade テンプレート](https://laravel.com/docs/blade)
- [Laravel コレクション](https://laravel.com/docs/collections)

---

**このサンプルを通じて、Laravel開発の基礎をしっかりと身につけましょう！**