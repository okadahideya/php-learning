# Lesson 07: フォーム処理（GET/POST）

## 学習目標
- HTMLフォームとPHPの連携を理解する
- GETとPOSTメソッドの違いと使い分けを覚える
- フォームデータのバリデーション（検証）を実装できるようになる
- CSRF対策の基本を理解する
- ユーザー入力のサニタイズができるようになる

## フォーム処理とは
Webアプリケーションでユーザーからのデータを受け取る最も一般的な方法です。お問い合わせフォーム、ログインフォーム、会員登録フォームなど、様々な場面で使用されます。

## HTTPメソッドの違い

### GETメソッド
- URLに?以降でパラメータを送信
- データが見える（URLに表示される）
- ブックマーク可能
- 検索やフィルタリングに適している
- データ量の制限がある

```php
// GETデータの取得
$search_query = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
```

### POSTメソッド
- HTTPリクエストのボディでデータを送信
- データが見えない
- 大量のデータを送信可能
- フォーム送信に適している
- セキュアな情報に適している

```php
// POSTデータの取得
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
```

## フォーム処理の基本パターン

### 1. フォーム送信の確認
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST送信時の処理
}
```

### 2. データの検証（バリデーション）
```php
$errors = [];

if (empty($_POST['name'])) {
    $errors[] = '名前は必須です。';
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = '有効なメールアドレスを入力してください。';
}
```

### 3. データのサニタイズ
```php
$clean_name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
$clean_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
```

## セキュリティ対策

### CSRF（Cross-Site Request Forgery）対策
```php
// トークン生成
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// フォームにトークンを埋め込み
echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';

// 送信時の検証
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('不正なリクエストです。');
}
```

### XSS（Cross-Site Scripting）対策
```php
// ユーザー入力は必ずエスケープ
$safe_output = htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

## 課題内容

### 1. sample.php を実行してみよう
Webサーバー（XAMPP、MAMP、PHP内蔵サーバーなど）を起動して、ブラウザでアクセスしてください。

```bash
# PHP内蔵サーバーで実行する場合
php -S localhost:8000
# ブラウザで http://localhost:8000/sample.php にアクセス
```

### 2. practice.php を完成させよう
practice.php に書かれているコメントの指示に従って、コードを書いてください。

#### 実装すべき内容
1. POSTメソッドでのフォーム送信処理
2. 各フィールドのバリデーション実装
3. エラーメッセージの表示
4. データのサニタイズ
5. JSONファイルへのデータ保存
6. GETパラメータに応じたページ表示制御
7. CSRF対策の実装
8. 送信成功時のデータ表示

### 3. 完成したら実行してみよう
```bash
php -S localhost:8000
# ブラウザで http://localhost:8000/practice.php にアクセス
```

## 主要な検証関数

### フィルター関数
```php
// メールアドレスの検証
filter_var($email, FILTER_VALIDATE_EMAIL)

// URLの検証
filter_var($url, FILTER_VALIDATE_URL)

// 数値の検証
filter_var($number, FILTER_VALIDATE_INT)

// データのサニタイズ
filter_var($string, FILTER_SANITIZE_STRING)
```

### 文字列関数
```php
// 空文字チェック
empty($value)

// 文字列長チェック
strlen($string)

// 正規表現マッチング
preg_match('/パターン/', $string)
```

## フォーム要素の値保持
```php
<!-- テキストフィールド -->
<input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

<!-- ラジオボタン -->
<input type="radio" name="gender" value="male" <?php echo (($_POST['gender'] ?? '') === 'male') ? 'checked' : ''; ?>>

<!-- チェックボックス -->
<input type="checkbox" name="hobbies[]" value="reading" <?php echo (in_array('reading', $_POST['hobbies'] ?? [])) ? 'checked' : ''; ?>>

<!-- セレクトボックス -->
<select name="prefecture">
    <option value="tokyo" <?php echo (($_POST['prefecture'] ?? '') === 'tokyo') ? 'selected' : ''; ?>>東京都</option>
</select>
```

## 実用的なバリデーション例

### 電話番号の検証
```php
if (!preg_match('/^0\d{1,4}-\d{1,4}-\d{4}$/', $phone)) {
    $errors[] = '電話番号の形式が正しくありません。';
}
```

### 日付の検証
```php
$date = DateTime::createFromFormat('Y-m-d', $_POST['birthday']);
if (!$date || $date->format('Y-m-d') !== $_POST['birthday']) {
    $errors[] = '生年月日の形式が正しくありません。';
}
```

### パスワード強度の検証
```php
if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $errors[] = 'パスワードは8文字以上で、大文字と数字を含む必要があります。';
}
```

## よくあるエラーと対策
1. **文字化け**: `header('Content-Type: text/html; charset=utf-8');` を設定
2. **CSRF攻撃**: トークンによる検証を必ず実装
3. **XSS攻撃**: 出力時のエスケープを忘れずに
4. **SQLインジェクション**: 後のレッスンで学習するPDOを使用

## 次のレッスン
フォーム処理ができたら、次はセッション管理とクッキーについて学習しましょう！