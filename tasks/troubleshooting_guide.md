# 統合トラブルシューティングガイド

PHP学習過程で遭遇する可能性のある問題と解決策をまとめたガイドです。

## 🔧 環境構築段階のトラブル

### PHP環境の問題

#### 「php: command not found」エラー
**問題**: PHPがインストールされていない、またはPATHが通っていない

**解決方法:**
```bash
# macOS (Homebrew)
brew install php

# Ubuntu/Linux
sudo apt update
sudo apt install php php-cli

# Windows
# XAMPPをインストールして環境変数PATHにPHPパスを追加
```

#### 「extension not loaded」エラー
**問題**: 必要なPHP拡張機能が有効になっていない

**解決方法:**
```bash
# 現在の拡張機能確認
php -m

# 不足している拡張機能を確認
php -m | grep -E 'pdo|mysql|sqlite|mbstring'

# Ubuntu/Linuxの場合
sudo apt install php-mysql php-sqlite3 php-mbstring php-xml

# php.iniで拡張機能を有効化
# 以下の行のコメントアウトを削除
extension=pdo_mysql
extension=pdo_sqlite
extension=mbstring
```

#### Composerインストール問題
**問題**: Composerがインストールできない、権限エラー

**解決方法:**
```bash
# グローバルインストール
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 権限問題の場合
sudo chown -R $(whoami) ~/.composer

# パーミッション確認
composer --version
```

### データベース接続問題

#### MySQL接続エラー「Connection refused」
**問題**: MySQLサーバーが起動していない、設定が間違っている

**解決方法:**
```bash
# MySQLサービス状況確認
sudo systemctl status mysql     # Linux
brew services list | grep mysql # macOS

# MySQLサービス開始
sudo systemctl start mysql      # Linux
brew services start mysql       # macOS

# 接続テスト
mysql -u root -p

# 設定確認
mysql -u root -p -e "SHOW VARIABLES LIKE 'port';"
```

#### 「Access denied for user」エラー
**問題**: 認証情報が間違っている、ユーザー権限がない

**解決方法:**
```sql
-- rootでログインしてユーザー作成
mysql -u root -p

-- ユーザー作成・権限付与
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON *.* TO 'app_user'@'localhost';
FLUSH PRIVILEGES;

-- .envファイル確認
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=app_user
DB_PASSWORD=secure_password
```

#### SQLiteファイル権限エラー
**問題**: SQLiteファイルへの読み書き権限がない

**解決方法:**
```bash
# ディレクトリ・ファイル権限設定
sudo chown -R www-data:www-data /path/to/database/
sudo chmod 664 /path/to/database/database.sqlite
sudo chmod 775 /path/to/database/

# 開発環境の場合
chmod 777 /path/to/database/
chmod 666 /path/to/database/database.sqlite
```

## 🌐 Web開発段階のトラブル

### Webサーバー関連

#### 「localhost拒否されました」エラー
**問題**: Webサーバーが起動していない、ポートが使用中

**解決方法:**
```bash
# PHP内蔵サーバー起動確認
php -S localhost:8000

# ポート使用状況確認
lsof -i :8000          # macOS/Linux
netstat -an | grep 8000 # Windows

# 別のポートで起動
php -S localhost:8080
```

#### 404エラー「Page not found」
**問題**: ルーティング設定、ファイルパスの問題

**解決方法:**
```bash
# ファイル存在確認
ls -la /path/to/your/file.php

# Webサーバーのドキュメントルート確認
# Apache の場合
sudo nano /etc/apache2/sites-available/000-default.conf

# PHP内蔵サーバーの場合、起動ディレクトリ確認
pwd
php -S localhost:8000 -t /path/to/public/
```

#### 500エラー「Internal Server Error」
**問題**: PHPコードのエラー、設定問題

**解決方法:**
```bash
# エラーログ確認
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx

# PHP エラー表示有効化（開発環境のみ）
# php.ini または .htaccess
display_errors = On
error_reporting = E_ALL

# Laravel の場合
php artisan log:clear
tail -f storage/logs/laravel.log
```

### Laravel特有の問題

#### 「No application encryption key」エラー
**問題**: アプリケーションキーが設定されていない

**解決方法:**
```bash
# アプリケーションキー生成
php artisan key:generate

# .envファイル確認
cat .env | grep APP_KEY

# 手動設定（必要に応じて）
cp .env.example .env
php artisan key:generate
```

#### 「Class not found」エラー
**問題**: オートロードが更新されていない、名前空間の問題

**解決方法:**
```bash
# Composer オートロード更新
composer dump-autoload

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 新しいクラス作成後の確認
composer require-checker # 依存関係チェック
```

#### マイグレーションエラー
**問題**: データベース接続、テーブル競合、構文エラー

**解決方法:**
```bash
# データベース接続確認
php artisan migrate:status

# マイグレーション実行（詳細表示）
php artisan migrate --verbose

# ロールバック
php artisan migrate:rollback

# 特定のマイグレーション確認
php artisan migrate --pretend

# データベースリセット（開発環境のみ）
php artisan migrate:fresh --seed
```

## 💻 開発・統合段階のトラブル

### Docker関連

#### 「Cannot connect to the Docker daemon」エラー
**問題**: Dockerサービスが起動していない、権限がない

**解決方法:**
```bash
# Dockerサービス状況確認
sudo systemctl status docker    # Linux
docker info                     # 全般

# Dockerサービス開始
sudo systemctl start docker     # Linux

# ユーザーをdockerグループに追加
sudo usermod -aG docker $USER
newgrp docker

# Docker Desktop起動確認（Windows/macOS）
```

#### 「Port already in use」エラー
**問題**: 指定ポートが既に使用されている

**解決方法:**
```bash
# ポート使用状況確認
docker ps -a
lsof -i :8080

# 使用中のコンテナ停止
docker stop $(docker ps -q)

# docker-compose.yml でポート変更
services:
  app:
    ports:
      - "8081:80"  # ホストポートを変更
```

#### コンテナビルドエラー
**問題**: Dockerfileの構文エラー、依存関係の問題

**解決方法:**
```bash
# ビルドログ詳細確認
docker build --no-cache -t your-image .

# 中間レイヤー確認
docker build --progress=plain -t your-image .

# 構文チェック
docker run --rm -i hadolint/hadolint < Dockerfile

# キャッシュ削除してリビルド
docker system prune -a
docker-compose build --no-cache
```

### Git・バージョン管理

#### 「fatal: not a git repository」エラー
**問題**: Gitリポジトリが初期化されていない

**解決方法:**
```bash
# 現在ディレクトリをGitリポジトリ化
git init

# リモートリポジトリから取得
git clone https://github.com/username/repository.git

# .gitディレクトリ確認
ls -la | grep .git
```

#### 「rejected (non-fast-forward)」エラー
**問題**: リモートリポジトリとの競合、push前にpullが必要

**解決方法:**
```bash
# リモートから変更取得
git pull origin main

# コンフリクト解決後
git add .
git commit -m "Resolve conflicts"
git push origin main

# 強制push（注意して使用）
git push --force-with-lease origin main
```

#### ブランチ切り替えエラー
**問題**: 未コミットの変更、ファイル競合

**解決方法:**
```bash
# 作業中の変更確認
git status

# 変更を一時保存
git stash
git checkout target-branch
git stash pop

# またはコミット
git add .
git commit -m "WIP: work in progress"
git checkout target-branch
```

### テスト関連

#### 「PHPUnit not found」エラー
**問題**: PHPUnitがインストールされていない、パスが通っていない

**解決方法:**
```bash
# Composer経由でインストール
composer require --dev phpunit/phpunit

# ベンダーディレクトリから実行
./vendor/bin/phpunit

# エイリアス設定
echo 'alias phpunit="./vendor/bin/phpunit"' >> ~/.bashrc
source ~/.bashrc
```

#### テストデータベースエラー
**問題**: テスト用データベース設定、権限問題

**解決方法:**
```bash
# .env.testing ファイル作成
cp .env .env.testing

# テスト用データベース設定
DB_CONNECTION=mysql
DB_DATABASE=your_app_test

# テストデータベース作成
mysql -u root -p -e "CREATE DATABASE your_app_test;"

# マイグレーション実行
php artisan migrate --env=testing
```

#### アサーションエラー
**問題**: 期待値と実際の値の不一致、テストロジックの問題

**解決方法:**
```bash
# 詳細なテスト出力
./vendor/bin/phpunit --verbose

# 特定のテスト実行
./vendor/bin/phpunit --filter testMethodName

# テストデバッグ
# テスト内でdd()やdump()を使用
dd($actualValue, $expectedValue);
```

## 🔒 セキュリティ・本番環境の問題

### セキュリティエラー

#### CSRF Token エラー
**問題**: フォーム送信時のCSRFトークン不一致

**解決方法:**
```html
<!-- HTMLフォームに追加 -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- フォーム内 -->
@csrf

<!-- Ajaxリクエスト -->
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>
```

#### 「403 Forbidden」エラー
**問題**: ファイル・ディレクトリの権限、認証の問題

**解決方法:**
```bash
# 権限確認・設定
ls -la /path/to/file

# Webサーバー権限設定
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

# Laravel ストレージ権限
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage bootstrap/cache
```

### パフォーマンス問題

#### ページ読み込み遅延
**問題**: 非効率なクエリ、N+1問題、キャッシュ不足

**解決方法:**
```bash
# Laravel デバッグバー有効化
composer require barryvdh/laravel-debugbar --dev

# クエリログ確認
DB::enableQueryLog();
// your code
dd(DB::getQueryLog());

# Eloquent N+1問題解決
$users = User::with('posts')->get(); // Eager Loading
```

#### メモリ不足エラー
**問題**: 大量データ処理、メモリリーク

**解決方法:**
```bash
# PHP メモリ制限確認・変更
php -i | grep memory_limit

# php.ini で設定変更
memory_limit = 512M

# Laravel チャンク処理
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // process user
    }
});
```

## 🆘 緊急時の対処法

### 本番環境でのエラー

#### サイト全体がダウン
**対処手順:**
```bash
# 1. エラーログ確認
tail -f /var/log/apache2/error.log
tail -f storage/logs/laravel.log

# 2. 直近の変更ロールバック
git log --oneline -5
git revert HEAD

# 3. データベースバックアップから復旧
mysql -u user -p database < backup.sql

# 4. 緊急メンテナンスページ表示
php artisan down --message="緊急メンテナンス中"
```

#### データベース破損
**対処手順:**
```bash
# 1. データベース状態確認
mysql -u root -p -e "CHECK TABLE your_table;"

# 2. 修復実行
mysql -u root -p -e "REPAIR TABLE your_table;"

# 3. バックアップから復元
mysql -u root -p database < latest_backup.sql

# 4. 整合性チェック
php artisan migrate:status
```

### データ復旧

#### 誤削除・誤更新
**対処手順:**
```bash
# 1. すぐに操作停止

# 2. Gitから復旧
git reflog
git reset --hard HEAD~1

# 3. データベースから復旧
# バックアップから特定テーブル復元
mysql -u root -p database < table_backup.sql

# 4. アプリケーションレベルでの復旧
# ソフトデリート使用の場合
User::withTrashed()->where('id', $id)->restore();
```

## 📞 サポート・情報収集

### 公式ドキュメント・コミュニティ
- **PHP**: https://www.php.net/manual/ja/
- **Laravel**: https://laravel.com/docs
- **MySQL**: https://dev.mysql.com/doc/
- **Docker**: https://docs.docker.com/

### 日本語コミュニティ
- **Laravel Japan**: Laravel.jp
- **PHP User Group**: php.net/ug.php
- **Qiita**: qiita.com (PHPタグ)
- **Stack Overflow**: stackoverflow.com (日本語版あり)

### エラー調査のポイント
1. **エラーメッセージを正確に記録**
2. **再現手順を明確にする**
3. **環境情報を整理する**（OS、PHPバージョン、依存関係）
4. **最小限の再現コードを作成**
5. **ログファイルを確認**

---

**困ったときは、まず落ち着いてエラーメッセージを読み、このガイドを参考に段階的に解決していきましょう！**