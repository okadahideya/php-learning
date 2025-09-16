# Laravel セットアップガイド

## 1. 環境準備

### 必要なソフトウェア
- PHP 8.0以上
- Composer
- SQLite または MySQL

### PHP拡張機能の確認
```bash
php -m | grep -E "(openssl|pdo|mbstring|tokenizer|xml|ctype|json|bcmath)"
```

必要な拡張機能が表示されない場合は、インストールしてください。

## 2. Composer インストール

### macOS
```bash
brew install composer
```

### Windows
[getcomposer.org](https://getcomposer.org/download/) からインストーラーをダウンロード

### Linux (Ubuntu)
```bash
sudo apt update
sudo apt install composer
```

## 3. Laravel プロジェクト作成

### 新しいプロジェクト作成
```bash
# プロジェクト作成
composer create-project laravel/laravel my-app

# ディレクトリ移動
cd my-app

# 開発サーバー起動
php artisan serve
```

### 既存プロジェクトのセットアップ
```bash
# 依存関係インストール
composer install

# 環境設定ファイルコピー
cp .env.example .env

# アプリケーションキー生成
php artisan key:generate

# データベース作成（SQLite使用の場合）
touch database/database.sqlite

# マイグレーション実行
php artisan migrate
```

## 4. 環境設定ファイル (.env)

### SQLite使用の場合
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:xxxxx
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### MySQL使用の場合
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

## 5. 初期データベースセットアップ

### マイグレーション実行
```bash
# マイグレーション実行
php artisan migrate

# 初期データ投入（オプション）
php artisan db:seed
```

## 6. 開発環境の確認

### サーバー起動確認
```bash
php artisan serve
```

ブラウザで http://localhost:8000 にアクセスして Laravel のウェルカムページが表示されることを確認。

### Artisan コマンド確認
```bash
# 利用可能なコマンド一覧
php artisan list

# バージョン確認
php artisan --version
```

## 7. よくある問題と解決法

### 権限エラー
```bash
# storage と cache ディレクトリの権限設定
chmod -R 775 storage bootstrap/cache
```

### Composer メモリエラー
```bash
# メモリ制限なしでComposer実行
php -d memory_limit=-1 /path/to/composer install
```

### SQLite データベースが作成されない
```bash
# データベースディレクトリの確認・作成
mkdir -p database
touch database/database.sqlite
```

## 8. IDE・エディタ設定

### VSCode 推奨拡張機能
- PHP Intelephense
- Laravel Blade Snippets
- Laravel goto view
- Laravel Extra Intellisense

### PhpStorm
Laravel プラグインを有効化して、プロジェクトルートで開く。

## 9. Git 設定

### 初期設定
```bash
git init
git add .
git commit -m "Initial Laravel setup"
```

### .gitignore 確認
Laravel デフォルトの .gitignore が適切に設定されていることを確認。

---

**セットアップが完了したら、sample ディレクトリの例を確認して Laravel の基本を学習しましょう！**