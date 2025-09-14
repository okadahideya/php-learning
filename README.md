# PHP学習教材

未経験エンジニア向けのPHP基礎学習教材です。実践的なコードで段階的にPHPの基本を学習できます。

## 学習の流れ

以下の順番で学習することをおすすめします：

1. **lesson01_variables** - 変数の基本
2. **lesson02_arrays** - 配列の基本
3. **lesson03_loops** - ループ処理
4. **lesson04_functions** - 関数の基本
5. **lesson05_classes** - クラスとオブジェクト指向（ファイル分割）
6. **lesson06_file_handling** - ファイル操作
7. **lesson07_form_handling** - フォーム処理（GET/POST）
8. **lesson08_sessions_cookies** - セッション・クッキー管理
9. **lesson09_database** - データベース操作（PDO）
10. **lesson10_error_handling** - エラーハンドリング

## 実行方法

### 基本レッスン（lesson01～04, lesson06, lesson09～10）
ターミナルで以下のコマンドを実行：

```bash
# レッスンディレクトリに移動
cd lesson01_variables

# sample.phpを実行（見本コード）
php sample.php

# practice.phpを実行（課題実装後）
php practice.php
```

### Webサーバーが必要なレッスン（lesson05, lesson07～08）
PHP内蔵サーバーを起動してブラウザで確認：

```bash
# レッスンディレクトリに移動
cd lesson07_form_handling

# PHP内蔵サーバーを起動
php -S localhost:8000

# ブラウザで以下にアクセス
# http://localhost:8000/sample.php
# http://localhost:8000/practice.php

# 停止する場合は Ctrl+C
```

## 各レッスンの構成

各レッスンディレクトリには以下のファイルがあります：

- **sample.php** - 見本コード（動作確認・学習用）
- **practice.php** - 課題用ファイル（実装練習用）
- **README.md** - 学習目標・基本概念・課題説明

## 学習のポイント

### ステップ1: sample.phpで理解
まず見本コードを実行して、どのように動作するかを確認しましょう。

```bash
php sample.php
```

### ステップ2: practice.phpで実装
コメントの指示に従って、自分でコードを実装してみましょう。

### ステップ3: 動作確認
実装後に実行して、期待通りに動作するか確認しましょう。

```bash
php practice.php
```

## 学習内容の要点

### 基礎編（lesson01～05）
- **変数**: PHPの基本的なデータ型と操作
- **配列**: インデックス配列と連想配列の使い分け
- **ループ**: for文、while文、foreach文の実用パターン
- **関数**: 再利用可能なコードの書き方
- **クラス**: オブジェクト指向の基本とファイル分割

### 実用編（lesson06～10）
- **ファイル操作**: テキスト、CSV、JSONファイルの読み書き
- **フォーム処理**: Webフォームとセキュリティ対策
- **セッション管理**: ログイン状態やカート機能
- **データベース**: SQLiteを使ったCRUD操作
- **エラー処理**: 堅牢なアプリケーション作成

## 重要なセキュリティポイント

### XSS対策
```php
// 出力時は必ずエスケープ
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

### CSRF対策
```php
// フォームにトークンを設置
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// 送信時に検証
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('不正なリクエスト');
}
```

### SQLインジェクション対策
```php
// プリペアドステートメントを使用
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
```

## トラブルシューティング

### よくあるエラーと対処法

#### 「php: command not found」
PHPがインストールされていません。

**macOS (Homebrew):**
```bash
brew install php
```

**Windows:**
[XAMPP](https://www.apachefriends.org/) をインストール

**Ubuntu/Linux:**
```bash
sudo apt update
sudo apt install php php-cli
```

#### ブラウザで表示されない
Webサーバーが起動していない可能性があります。

```bash
# PHP内蔵サーバーを起動
php -S localhost:8000

# ブラウザで http://localhost:8000 にアクセス
```

#### データベースエラー
SQLite拡張が有効になっているか確認：

```bash
php -m | grep sqlite
```

表示されない場合は、php.iniで以下を有効化：
```ini
extension=sqlite3
extension=pdo_sqlite
```

## 推奨環境

- **PHP**: 7.4以上（8.0以上推奨）
- **OS**: Windows, macOS, Linux
- **エディタ**: VSCode, PhpStorm, Sublime Text など
- **ブラウザ**: Chrome, Firefox, Safari など（モダンブラウザ）

## 次のステップ

この教材を完了したら、以下の学習をおすすめします：

1. **フレームワーク**: Laravel, Symfony
2. **フロントエンド**: HTML/CSS/JavaScript
3. **データベース**: MySQL, PostgreSQL
4. **開発環境**: Docker, Git
5. **テスト**: PHPUnit

## サポート

各レッスンのREADME.mdに詳しい説明があります。
わからないことがあれば、まず該当レッスンのREADME.mdを確認してください。

---

**Happy Coding! 🚀**