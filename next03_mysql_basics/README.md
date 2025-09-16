# Next Step 03: MySQL実用

## 学習目標
- MySQL の基本操作とSQL文を習得する
- 効率的なデータベース設計を理解する
- インデックスとパフォーマンス最適化を学ぶ
- PHPとMySQLの実用的な連携を身につける

## 前提条件
- PHP基礎学習完了
- SQLiteでのデータベース操作経験
- 基本的なSQL文の理解

## 環境構築

### MySQL のインストール

#### macOS (Homebrew)
```bash
brew install mysql
brew services start mysql
```

#### Windows
[MySQL公式サイト](https://dev.mysql.com/downloads/) からMySQL Installerをダウンロード

#### Ubuntu/Linux
```bash
sudo apt update
sudo apt install mysql-server
sudo mysql_secure_installation
```

### 初期設定
```bash
# MySQL にログイン
mysql -u root -p

# データベース作成
CREATE DATABASE php_learning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# ユーザー作成（本番環境では必須）
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON php_learning.* TO 'app_user'@'localhost';
FLUSH PRIVILEGES;
```

## 学習内容

### 1. MySQL基本操作
- データベース・テーブル作成
- CRUD操作（SELECT, INSERT, UPDATE, DELETE）
- データ型の適切な選択
- 制約（PRIMARY KEY, FOREIGN KEY, UNIQUE）

### 2. テーブル設計
- 正規化の基本
- リレーション設計
- パフォーマンスを考慮した設計

### 3. 高度なSQL
- JOINクエリ
- サブクエリ
- 集計関数とGROUP BY
- インデックス戦略

### 4. PHP-MySQL連携
- PDOでのMySQL接続
- プリペアドステートメント
- トランザクション処理
- エラーハンドリング

## 実行方法

### サンプル確認
```bash
cd sample
# setup.sql でテーブル作成
mysql -u root -p php_learning < setup.sql

# PHPサンプル実行
php -S localhost:8000
```

### 実践課題
```bash
cd practice
# 課題用データベース準備
mysql -u root -p php_learning < practice_setup.sql

# 実装・テスト
php practice.php
```

## 基本的なSQL操作

### データベース・テーブル作成
```sql
-- データベース作成
CREATE DATABASE shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shop;

-- ユーザーテーブル
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 商品テーブル
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category_id),
    INDEX idx_price (price)
);
```

### 基本的なクエリ
```sql
-- データ挿入
INSERT INTO users (name, email, password) VALUES
('田中太郎', 'tanaka@example.com', 'hashed_password'),
('佐藤花子', 'sato@example.com', 'hashed_password');

-- データ取得
SELECT * FROM users WHERE email = 'tanaka@example.com';
SELECT name, email FROM users ORDER BY created_at DESC LIMIT 10;

-- データ更新
UPDATE users SET name = '田中次郎' WHERE id = 1;

-- データ削除
DELETE FROM users WHERE id = 1;
```

## 高度なSQL

### JOIN クエリ
```sql
-- INNER JOIN
SELECT u.name, p.name as product_name, p.price
FROM users u
INNER JOIN orders o ON u.id = o.user_id
INNER JOIN products p ON o.product_id = p.id;

-- LEFT JOIN（全ユーザーと注文情報）
SELECT u.name, COUNT(o.id) as order_count
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
GROUP BY u.id, u.name;
```

### 集計と分析
```sql
-- 月別売上集計
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as order_count,
    SUM(total) as total_sales
FROM orders 
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;

-- 商品別売上ランキング
SELECT 
    p.name,
    SUM(oi.quantity) as total_sold,
    SUM(oi.price * oi.quantity) as revenue
FROM products p
JOIN order_items oi ON p.id = oi.product_id
GROUP BY p.id, p.name
ORDER BY revenue DESC
LIMIT 10;
```

## インデックス戦略

### 効果的なインデックス
```sql
-- 単一カラムインデックス
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_created_at ON orders(created_at);

-- 複合インデックス
CREATE INDEX idx_user_status ON orders(user_id, status);
CREATE INDEX idx_price_category ON products(category_id, price);

-- インデックス使用状況確認
EXPLAIN SELECT * FROM orders WHERE user_id = 1 AND status = 'completed';
```

### パフォーマンス最適化
```sql
-- スロークエリ確認
SHOW VARIABLES LIKE 'slow_query_log';
SET GLOBAL slow_query_log = 'ON';

-- インデックス効果確認
SHOW INDEX FROM products;
ANALYZE TABLE products;
```

## PHP-MySQL連携

### 接続とCRUD操作
```php
<?php
class Database {
    private $pdo;
    
    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=php_learning;charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $this->pdo = new PDO($dsn, 'app_user', 'secure_password', $options);
    }
    
    public function getUsers($limit = 10) {
        $stmt = $this->pdo->prepare('SELECT * FROM users ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function createUser($name, $email, $password) {
        $stmt = $this->pdo->prepare('
            INSERT INTO users (name, email, password) 
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    }
    
    public function updateUser($id, $name, $email) {
        $stmt = $this->pdo->prepare('
            UPDATE users SET name = ?, email = ?, updated_at = NOW() 
            WHERE id = ?
        ');
        return $stmt->execute([$name, $email, $id]);
    }
}
?>
```

### トランザクション処理
```php
<?php
public function transferOrder($fromUserId, $toUserId, $amount) {
    try {
        $this->pdo->beginTransaction();
        
        // 送金者の残高確認・減額
        $stmt = $this->pdo->prepare('
            UPDATE accounts SET balance = balance - ? 
            WHERE user_id = ? AND balance >= ?
        ');
        $stmt->execute([$amount, $fromUserId, $amount]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('残高不足です');
        }
        
        // 受取者の残高増額
        $stmt = $this->pdo->prepare('
            UPDATE accounts SET balance = balance + ? 
            WHERE user_id = ?
        ');
        $stmt->execute([$amount, $toUserId]);
        
        $this->pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}
?>
```

## 実装課題

### 課題1: ECサイトデータベース設計
1. ユーザー、商品、カテゴリ、注文テーブル設計
2. 適切な制約とインデックス設定
3. サンプルデータ投入

### 課題2: 商品管理システム
1. 商品CRUD操作
2. カテゴリ別商品一覧
3. 在庫管理機能

### 課題3: 注文管理システム
1. 注文作成・更新
2. 売上レポート生成
3. パフォーマンス最適化

## セキュリティ対策

### SQLインジェクション対策
```php
// 危険: 直接SQL文に値を埋め込み
$sql = "SELECT * FROM users WHERE email = '{$_POST['email']}'"; // ❌

// 安全: プリペアドステートメント使用
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$_POST['email']]); // ✅
```

### 接続セキュリティ
```php
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false, // SQLインジェクション対策
    PDO::ATTR_PERSISTENT => false,       // 持続的接続を無効化
];
```

## 運用・保守

### バックアップ
```bash
# データベース全体をバックアップ
mysqldump -u root -p php_learning > backup.sql

# 特定テーブルのみバックアップ
mysqldump -u root -p php_learning users products > tables_backup.sql

# リストア
mysql -u root -p php_learning < backup.sql
```

### パフォーマンス監視
```sql
-- プロセス一覧
SHOW PROCESSLIST;

-- テーブルサイズ確認
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size in MB'
FROM information_schema.tables 
WHERE table_schema = 'php_learning';
```

## 次のステップ
MySQL実用をマスターしたら、次は開発環境構築（Docker/Git）を学習しましょう！