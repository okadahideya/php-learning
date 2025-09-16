# Next Step 01: Laravel基礎

## 学習目標
- Laravelフレームワークの基本概念を理解する
- ルーティング・コントローラー・ビューの連携を覚える
- Bladeテンプレートエンジンの使い方を習得する
- 簡単なCRUDアプリケーションを作成できるようになる

## 前提条件
- PHP基礎学習（lesson01～10）完了
- Composer がインストール済み
- 基本的なターミナル操作ができる

## Laravel とは
LaravelはPHPで最も人気のあるWebフレームワークです。美しい文法と豊富な機能で、効率的なWeb開発を可能にします。

## セットアップ

### 1. Composerでプロジェクト作成
```bash
composer create-project laravel/laravel sample-app
cd sample-app
```

### 2. 開発サーバー起動
```bash
php artisan serve
```

### 3. ブラウザで確認
http://localhost:8000 にアクセス

## 学習の流れ

### Step 1: 基本構造の理解
```bash
# sample ディレクトリの見本を確認
cd sample
php artisan serve
```

### Step 2: 実践課題
```bash
# practice ディレクトリで実装
cd practice
composer create-project laravel/laravel task-manager
```

### Step 3: 動作確認
作成したアプリケーションが正常に動作することを確認

## Laravel の基本概念

### MVC アーキテクチャ
- **Model**: データとビジネスロジック
- **View**: ユーザーインターフェース（Bladeテンプレート）
- **Controller**: リクエストの処理とレスポンス

### ルーティング
```php
// routes/web.php
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
```

### コントローラー
```php
// app/Http/Controllers/UserController.php
public function index() {
    $users = User::all();
    return view('users.index', compact('users'));
}
```

### Bladeテンプレート
```blade
{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
    @foreach($users as $user)
        <p>{{ $user->name }}</p>
    @endforeach
@endsection
```

## 実装課題

### 課題1: 基本的なページ作成
1. ルートページの作成
2. About ページの追加
3. ナビゲーションメニューの実装

### 課題2: データ表示
1. ユーザー一覧ページ
2. ユーザー詳細ページ
3. データの受け渡し

### 課題3: CRUDアプリケーション
1. タスク管理システムの作成
2. 作成・読み取り・更新・削除機能
3. フォーム処理とバリデーション

## 重要なArtisanコマンド

### プロジェクト作成・管理
```bash
# プロジェクト作成
composer create-project laravel/laravel project-name

# 開発サーバー起動
php artisan serve

# キャッシュクリア
php artisan cache:clear
```

### ファイル生成
```bash
# コントローラー作成
php artisan make:controller UserController

# モデル作成
php artisan make:model User

# マイグレーション作成
php artisan make:migration create_users_table
```

### データベース
```bash
# マイグレーション実行
php artisan migrate

# シーダー実行
php artisan db:seed
```

## ディレクトリ構造

```
laravel-project/
├── app/
│   ├── Http/Controllers/    # コントローラー
│   ├── Models/             # モデル
│   └── ...
├── resources/
│   ├── views/              # Bladeテンプレート
│   └── ...
├── routes/
│   ├── web.php            # Webルート
│   └── ...
├── database/
│   ├── migrations/        # マイグレーション
│   └── ...
└── public/                # 公開ディレクトリ
```

## トラブルシューティング

### よくあるエラー

#### 「Class 'App\\Http\\Controllers\\Controller' not found」
```bash
composer install
php artisan cache:clear
```

#### データベース接続エラー
```bash
# .env ファイルでデータベース設定を確認
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

#### Artisan コマンドが動かない
```bash
# Composer の自動読み込み再生成
composer dump-autoload
```

### 推奨環境
- PHP 8.0以上
- Composer 2.0以上
- SQLite または MySQL

## 次のステップ
Laravel基礎をマスターしたら、次はフロントエンド連携を学習しましょう！

## 参考資料
- [Laravel公式ドキュメント](https://laravel.com/docs)
- [Laravel日本語ドキュメント](https://readouble.com/laravel/)
- [Laracasts](https://laracasts.com/) - Laravel学習動画