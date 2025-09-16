# MySQL基礎学習サンプル - ECサイトデータベース

PHP学習者向けのMySQL実用サンプル集です。実際のECサイトを想定したデータベース設計と操作を学習できます。

## 📋 概要

このサンプルでは、以下のMySQL関連技術を学習できます：

- **データベース設計**: 正規化・リレーション・インデックス設計
- **SQL操作**: CRUD・JOIN・サブクエリ・ウィンドウ関数
- **PHP連携**: PDO接続・プリペアドステートメント・トランザクション
- **パフォーマンス**: インデックス最適化・クエリチューニング
- **実用パターン**: ECサイトの典型的なデータ操作

## 🚀 セットアップ方法

### 1. 前提条件
```bash
# MySQL 8.0以上
mysql --version

# PHP 8.0以上（PDO拡張有効）
php -m | grep pdo
```

### 2. データベース作成
```bash
# MySQLにログイン
mysql -u root -p

# データベース作成とサンプルデータ投入
source setup.sql
```

### 3. ファイル構成確認
```
sample/
├── setup.sql           # データベース作成・初期データ
├── queries.sql         # 実用SQLクエリ集
├── connection.php      # PHP接続サンプル
└── README.md          # このファイル
```

### 4. 接続設定
`connection.php`の設定を環境に合わせて変更：
```php
class DatabaseConfig {
    const HOST = 'localhost';        // MySQLホスト
    const DATABASE = 'ecommerce_sample';  // データベース名
    const USERNAME = 'root';              // ユーザー名
    const PASSWORD = '';                  // パスワード
}
```

## 🗄️ データベース設計

### テーブル構成
```
categories          # 商品カテゴリ
├── products        # 商品情報
│   └── product_images  # 商品画像
├── users           # ユーザー情報
│   └── user_addresses  # ユーザー住所
└── orders          # 注文情報
    └── order_items # 注文商品詳細
cart_items          # カート情報
admin_users         # 管理者ユーザー
```

### リレーション設計
```sql
-- 1対多のリレーション
categories (1) ──── (多) products
users (1) ──── (多) orders
orders (1) ──── (多) order_items

-- 外部キー制約
products.category_id → categories.id
orders.user_id → users.id
order_items.order_id → orders.id
order_items.product_id → products.id
```

### インデックス戦略
```sql
-- 基本インデックス
INDEX idx_category_id (category_id)
INDEX idx_created_at (created_at)

-- 複合インデックス
INDEX idx_category_active (category_id, is_active)
INDEX idx_price_active (price, is_active)

-- ユニークインデックス
UNIQUE INDEX idx_email (email)
UNIQUE INDEX idx_sku (sku)
```

## 🔍 主要な学習ポイント

### 1. 基本的なSQL操作

#### SELECT文の基礎
```sql
-- 基本的な商品検索
SELECT 
    p.name,
    p.price,
    c.name as category_name
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.is_active = TRUE
ORDER BY p.created_at DESC;
```

#### 条件分岐（CASE文）
```sql
-- 価格帯による分類
SELECT 
    name,
    price,
    CASE 
        WHEN price < 5000 THEN '安価'
        WHEN price < 50000 THEN '中価格'
        ELSE '高価格'
    END as price_range
FROM products;
```

### 2. 高度なSQL操作

#### 集計関数とGROUP BY
```sql
-- カテゴリ別売上分析
SELECT 
    c.name as category,
    COUNT(p.id) as product_count,
    SUM(oi.total_price) as total_sales,
    AVG(oi.unit_price) as avg_price
FROM categories c
JOIN products p ON c.id = p.category_id
JOIN order_items oi ON p.id = oi.product_id
GROUP BY c.id, c.name
ORDER BY total_sales DESC;
```

#### サブクエリ（相関・非相関）
```sql
-- 平均価格より高い商品
SELECT name, price
FROM products p1
WHERE price > (
    SELECT AVG(price) 
    FROM products p2 
    WHERE p2.category_id = p1.category_id
);
```

#### ウィンドウ関数
```sql
-- 売上ランキング（カテゴリ内順位）
SELECT 
    name,
    category_id,
    revenue,
    RANK() OVER (PARTITION BY category_id ORDER BY revenue DESC) as rank_in_category
FROM (
    SELECT 
        p.name,
        p.category_id,
        SUM(oi.total_price) as revenue
    FROM products p
    JOIN order_items oi ON p.id = oi.product_id
    GROUP BY p.id
) product_sales;
```

### 3. PHP-MySQL連携

#### PDO接続パターン
```php
// 基本的な接続
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=ecommerce_sample;charset=utf8mb4',
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die('接続エラー: ' . $e->getMessage());
}
```

#### プリペアドステートメント
```php
// 安全なSQL実行
$sql = "SELECT * FROM products WHERE category_id = :category_id AND price <= :max_price";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':category_id' => $categoryId,
    ':max_price' => $maxPrice
]);
$products = $stmt->fetchAll();
```

#### トランザクション処理
```php
$pdo->beginTransaction();
try {
    // 複数のSQL実行
    $pdo->exec($sql1);
    $pdo->exec($sql2);
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    throw $e;
}
```

### 4. パフォーマンス最適化

#### EXPLAIN文による実行計画確認
```sql
EXPLAIN FORMAT=JSON
SELECT p.*, c.name as category_name
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.is_active = TRUE
ORDER BY p.created_at DESC
LIMIT 10;
```

#### インデックス効果の検証
```sql
-- インデックス使用前後の比較
SHOW INDEX FROM products;
EXPLAIN SELECT * FROM products WHERE category_id = 1;
```

## 🛠️ 実用的なクエリ例

### 1. ECサイトの典型的な処理

#### 商品検索（フィルタリング）
```sql
SELECT 
    p.id,
    p.name,
    CASE WHEN p.sale_price IS NOT NULL THEN p.sale_price ELSE p.price END as current_price,
    p.stock_quantity,
    c.name as category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.is_active = TRUE
  AND (p.name LIKE '%検索キーワード%' OR p.description LIKE '%検索キーワード%')
  AND p.category_id IN (1, 2, 3)
  AND p.price BETWEEN 1000 AND 50000
ORDER BY p.is_featured DESC, p.created_at DESC
LIMIT 20;
```

#### 注文履歴の表示
```sql
SELECT 
    o.order_number,
    o.status,
    o.total_amount,
    o.created_at,
    GROUP_CONCAT(
        CONCAT(oi.product_name, ' x', oi.quantity) 
        SEPARATOR ', '
    ) as items
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE o.user_id = ?
GROUP BY o.id
ORDER BY o.created_at DESC;
```

### 2. 管理画面向け分析クエリ

#### 売上レポート
```sql
-- 月別売上推移
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as order_count,
    SUM(total_amount) as total_sales,
    AVG(total_amount) as avg_order_value
FROM orders
WHERE status IN ('shipped', 'delivered')
  AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;
```

#### 在庫アラート
```sql
-- 在庫切れ・在庫少商品
SELECT 
    p.name,
    p.sku,
    p.stock_quantity,
    c.name as category_name,
    CASE 
        WHEN p.stock_quantity = 0 THEN '在庫切れ'
        WHEN p.stock_quantity <= 5 THEN '在庫少'
    END as alert_type
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.is_active = TRUE
  AND p.manage_stock = TRUE
  AND p.stock_quantity <= 5
ORDER BY p.stock_quantity ASC;
```

## 🔧 実行とテスト

### コマンドライン実行
```bash
# 接続テスト
php connection.php

# SQLクエリ実行
mysql -u root -p ecommerce_sample < queries.sql
```

### PHPスクリプトでのテスト
```php
<?php
require_once 'connection.php';

// 接続テスト
$pdo = createAdvancedConnection();

// 商品データ取得テスト
$products = getProducts($pdo, 5);
print_r($products);

// トランザクションテスト
$orderItems = [
    [
        'product_id' => 1,
        'product_name' => 'テスト商品',
        'product_sku' => 'TEST-001',
        'quantity' => 2,
        'unit_price' => 1000,
        'total_price' => 2000
    ]
];
$orderId = processOrder($pdo, 1, $orderItems);
echo "注文ID: {$orderId}\n";
?>
```

## 📊 データ分析例

### RFM分析（顧客分析）
```sql
-- Recency（最新購入日）、Frequency（購入回数）、Monetary（購入金額）
SELECT 
    u.id,
    CONCAT(u.last_name, ' ', u.first_name) as customer_name,
    COUNT(o.id) as frequency,                    -- 購入回数
    MAX(o.created_at) as last_order_date,        -- 最新購入日
    DATEDIFF(NOW(), MAX(o.created_at)) as recency, -- 経過日数
    SUM(o.total_amount) as monetary              -- 総購入金額
FROM users u
LEFT JOIN orders o ON u.id = o.user_id AND o.status IN ('shipped', 'delivered')
GROUP BY u.id
HAVING COUNT(o.id) > 0
ORDER BY monetary DESC, frequency DESC;
```

### 商品レコメンデーション基礎
```sql
-- よく一緒に買われる商品
SELECT 
    p1.name as product1,
    p2.name as product2,
    COUNT(*) as co_occurrence
FROM order_items oi1
JOIN order_items oi2 ON oi1.order_id = oi2.order_id AND oi1.product_id < oi2.product_id
JOIN products p1 ON oi1.product_id = p1.id
JOIN products p2 ON oi2.product_id = p2.id
GROUP BY oi1.product_id, oi2.product_id
HAVING co_occurrence >= 2
ORDER BY co_occurrence DESC;
```

## 🚀 発展学習

このサンプルをベースに以下の技術を学習できます：

### 1. 高度なMySQL機能
- **ストアドプロシージャ**: 複雑なビジネスロジックの実装
- **トリガー**: データ変更時の自動処理
- **ビュー**: 複雑なクエリの抽象化
- **パーティショニング**: 大量データの分割管理

### 2. パフォーマンス最適化
- **クエリキャッシュ**: 頻繁なクエリの高速化
- **インデックス戦略**: 複合インデックス・部分インデックス
- **レプリケーション**: 読み込み性能の向上
- **シャーディング**: 水平スケーリング

### 3. セキュリティ
- **SQLインジェクション対策**: プリペアドステートメント
- **権限管理**: ユーザー別アクセス制御
- **データ暗号化**: 機密情報の保護
- **監査ログ**: データ変更の追跡

### 4. 運用管理
- **バックアップ戦略**: mysqldump・バイナリログ
- **ログ監視**: スロークエリ・エラーログ
- **メトリクス監視**: パフォーマンス指標の追跡

## 📚 参考資料

- [MySQL 8.0 Reference Manual](https://dev.mysql.com/doc/refman/8.0/en/) - MySQL公式ドキュメント
- [PHP PDO Documentation](https://www.php.net/manual/ja/book.pdo.php) - PHP PDO公式マニュアル
- [SQL Performance Explained](https://sql-performance-explained.com/) - SQL性能最適化ガイド
- [High Performance MySQL](https://www.oreilly.com/library/view/high-performance-mysql/9781449332471/) - MySQL高性能化書籍

## 🔍 トラブルシューティング

### よくあるエラーと対処法

#### 1. 接続エラー
```
SQLSTATE[HY000] [2002] Connection refused
```
**対処法**: MySQLサービスが起動しているか確認
```bash
# macOS
brew services start mysql

# Linux
sudo systemctl start mysql
```

#### 2. 文字化け
```
文字化け: ????????????
```
**対処法**: 文字セット設定確認
```sql
SHOW VARIABLES LIKE 'character_set%';
SET NAMES utf8mb4;
```

#### 3. 権限エラー
```
Access denied for user 'root'@'localhost'
```
**対処法**: ユーザー権限確認・付与
```sql
GRANT ALL PRIVILEGES ON ecommerce_sample.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

---

**このサンプルを通じて、実用的なMySQL操作とPHP連携を身につけましょう！**