# Lesson 09: データベース操作（PDO）

## 学習目標
- PDOを使ったデータベース接続ができるようになる
- 基本的なCRUD操作（作成・読み取り・更新・削除）を理解する
- プリペアドステートメントの使い方を覚える
- SQLインジェクション対策を理解する
- 集計クエリの基本を覚える

## PDOとは
PHP Data Objects（PDO）は、PHPでデータベースにアクセスするための標準的な方法です。MySQL、PostgreSQL、SQLiteなど、複数のデータベースに統一的な方法でアクセスできます。

## 基本的な操作

### 接続
```php
// SQLite接続例
$pdo = new PDO('sqlite:database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// MySQL接続例
// $pdo = new PDO('mysql:host=localhost;dbname=test', $user, $pass);
```

### CREATE（作成）
```php
// テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE
)";
$pdo->exec($sql);

// データ挿入
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['太郎', 'taro@example.com']);
```

### READ（読み取り）
```php
// 全データ取得
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 条件付き取得
$stmt = $pdo->prepare("SELECT * FROM users WHERE age > ?");
$stmt->execute([20]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 1件のみ取得
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
```

### UPDATE（更新）
```php
$stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
$stmt->execute(['新しい名前', 1]);
$affected = $stmt->rowCount(); // 更新された行数
```

### DELETE（削除）
```php
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([1]);
$deleted = $stmt->rowCount(); // 削除された行数
```

## 課題内容

### 1. sample.php を実行してみよう
```bash
php sample.php
```

### 2. practice.php を完成させよう

#### 実装すべき内容
1. SQLiteデータベースへの接続
2. booksテーブルの作成
3. 書籍データの挿入
4. 全書籍の取得・表示（価格順）
5. 条件付きデータ取得（3000円以上）
6. データ件数のカウント（2023年出版）
7. 最高価格・最低価格の書籍取得
8. データの更新（価格変更）
9. データの削除（2021年出版削除）
10. 集計クエリ（著者別統計）

### 3. 完成したら実行してみよう
```bash
php practice.php
```

## プリペアドステートメント
SQLインジェクション攻撃を防ぐための仕組みです。

### 危険な例（絶対にやってはいけない）
```php
// SQLインジェクションの危険性あり
$sql = "SELECT * FROM users WHERE name = '{$_POST['name']}'";
```

### 安全な例
```php
// プリペアドステートメントで安全
$stmt = $pdo->prepare("SELECT * FROM users WHERE name = ?");
$stmt->execute([$_POST['name']]);
```

## よく使う集計関数
- `COUNT()`: 件数をカウント
- `SUM()`: 合計値を計算
- `AVG()`: 平均値を計算
- `MAX()`: 最大値を取得
- `MIN()`: 最小値を取得

## フェッチモード
- `PDO::FETCH_ASSOC`: 連想配列で取得
- `PDO::FETCH_NUM`: 数値インデックス配列で取得
- `PDO::FETCH_OBJ`: オブジェクトで取得

## エラーハンドリング
```php
try {
    $pdo = new PDO('sqlite:database.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // データベース操作
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}
```

## 実用例
- ユーザー管理システム
- 商品カタログ
- ブログシステム
- 在庫管理システム
- アンケートシステム

## セキュリティのポイント
1. 必ずプリペアドステートメントを使用
2. エラーメッセージでSQL構造を露出させない
3. 適切な権限設定
4. 入力値の検証

## 次のレッスン
データベース操作ができたら、最後にエラーハンドリングについて学習しましょう！