# Lesson 08: セッション・クッキー管理

## 学習目標
- セッションの開始・使用・終了ができるようになる
- クッキーの設定・読み取り・削除ができるようになる
- セッションとクッキーの違いを理解する
- 実用的な用途（ログイン状態管理、設定保存）を理解する

## セッションとクッキーの違い

| 項目 | セッション | クッキー |
|------|-----------|----------|
| 保存場所 | サーバー側 | ブラウザ側 |
| 容量制限 | サーバー次第 | 4KB程度 |
| セキュリティ | 高い | 低い |
| 有効期限 | ブラウザ終了まで | 指定可能 |
| 用途 | ログイン状態など | 設定・環境設定 |

## セッションの基本操作

### 開始・データ操作
```php
// セッション開始（必須）
session_start();

// データ保存
$_SESSION['user_id'] = 123;
$_SESSION['username'] = '太郎';

// データ取得
$user_id = $_SESSION['user_id'] ?? null;

// データ削除
unset($_SESSION['user_id']);

// セッション全削除
session_destroy();
```

## クッキーの基本操作

### 設定・取得・削除
```php
// クッキー設定（30日有効）
setcookie('username', 'taro', time() + (86400 * 30));

// クッキー取得
$username = $_COOKIE['username'] ?? '';

// クッキー削除（過去の時刻を設定）
setcookie('username', '', time() - 3600);
```

## 課題内容

### 1. sample.php を実行してみよう
```bash
php -S localhost:8000
# ブラウザで http://localhost:8000/sample.php にアクセス
```

### 2. practice.php を完成させよう

#### 実装すべき内容
1. セッション開始
2. 訪問回数のカウント機能
3. 最終訪問時刻の記録
4. ユーザー名のクッキー保存
5. POSTアクションの処理分岐
6. セッション情報の表示
7. クッキー情報の表示
8. デバッグ情報の表示

### 3. 完成したら実行してみよう
```bash
php -S localhost:8000
# ブラウザで http://localhost:8000/practice.php にアクセス
```

## よくある用途

### セッション
- ログイン状態の管理
- ショッピングカートの内容
- フォームの一時保存
- CSRF対策トークン

### クッキー
- ユーザー設定（言語、テーマ）
- 「次回から自動ログイン」機能
- 訪問履歴
- 広告表示設定

## セキュリティ注意点

### セッション
```php
// セッションハイジャック対策
session_regenerate_id(true);

// HTTPS必須設定
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
```

### クッキー
```php
// XSS対策（JavaScript無効）
setcookie('name', 'value', time() + 3600, '/', '', true, true);
//                                        path domain secure httponly
```

## 主要な関数
- `session_start()`: セッション開始
- `session_destroy()`: セッション削除
- `session_regenerate_id()`: セッションID再生成
- `setcookie()`: クッキー設定
- `time()`: 現在時刻取得

## 次のレッスン
セッション・クッキー管理ができたら、次はデータベース操作について学習しましょう！