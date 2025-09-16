<?php
/**
 * MySQL接続サンプル - 実用的なデータベース接続例
 * PHP学習者向けのMySQL接続パターン集
 */

// ===== 設定クラス =====
class DatabaseConfig {
    const HOST = 'localhost';
    const DATABASE = 'ecommerce_sample';
    const USERNAME = 'root';
    const PASSWORD = '';
    const CHARSET = 'utf8mb4';
    const OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
}

// ===== 基本的な接続パターン =====

/**
 * 1. シンプルなPDO接続
 */
function createSimpleConnection() {
    try {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DatabaseConfig::HOST,
            DatabaseConfig::DATABASE,
            DatabaseConfig::CHARSET
        );
        
        $pdo = new PDO($dsn, DatabaseConfig::USERNAME, DatabaseConfig::PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ データベース接続成功\n";
        return $pdo;
        
    } catch (PDOException $e) {
        echo "❌ 接続エラー: " . $e->getMessage() . "\n";
        return null;
    }
}

/**
 * 2. 設定オプション付きPDO接続
 */
function createAdvancedConnection() {
    try {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DatabaseConfig::HOST,
            DatabaseConfig::DATABASE,
            DatabaseConfig::CHARSET
        );
        
        $pdo = new PDO($dsn, DatabaseConfig::USERNAME, DatabaseConfig::PASSWORD, DatabaseConfig::OPTIONS);
        
        echo "✅ 高度なデータベース接続成功\n";
        return $pdo;
        
    } catch (PDOException $e) {
        echo "❌ 接続エラー: " . $e->getMessage() . "\n";
        return null;
    }
}

// ===== データベースクラス（シングルトンパターン） =====
class Database {
    private static $instance = null;
    private $connection = null;
    
    private function __construct() {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DatabaseConfig::HOST,
                DatabaseConfig::DATABASE,
                DatabaseConfig::CHARSET
            );
            
            $this->connection = new PDO($dsn, DatabaseConfig::USERNAME, DatabaseConfig::PASSWORD, DatabaseConfig::OPTIONS);
            
        } catch (PDOException $e) {
            throw new Exception("データベース接続エラー: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // 接続を閉じる
    public function closeConnection() {
        $this->connection = null;
    }
}

// ===== 基本的なCRUD操作例 =====

/**
 * 商品データを取得するサンプル
 */
function getProducts($pdo, $limit = 10) {
    try {
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.sale_price,
                p.stock_quantity,
                c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = TRUE
            ORDER BY p.created_at DESC
            LIMIT :limit
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $products = $stmt->fetchAll();
        
        echo "📦 商品データ取得成功: " . count($products) . "件\n";
        return $products;
        
    } catch (PDOException $e) {
        echo "❌ 商品取得エラー: " . $e->getMessage() . "\n";
        return [];
    }
}

/**
 * 新しい商品を追加するサンプル
 */
function addProduct($pdo, $productData) {
    try {
        $sql = "
            INSERT INTO products (
                category_id, name, slug, description, sku, 
                price, stock_quantity, is_featured, created_at
            ) VALUES (
                :category_id, :name, :slug, :description, :sku,
                :price, :stock_quantity, :is_featured, NOW()
            )
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':category_id' => $productData['category_id'],
            ':name' => $productData['name'],
            ':slug' => $productData['slug'],
            ':description' => $productData['description'],
            ':sku' => $productData['sku'],
            ':price' => $productData['price'],
            ':stock_quantity' => $productData['stock_quantity'],
            ':is_featured' => $productData['is_featured'] ?? false
        ]);
        
        $productId = $pdo->lastInsertId();
        echo "✅ 商品追加成功: ID = {$productId}\n";
        return $productId;
        
    } catch (PDOException $e) {
        echo "❌ 商品追加エラー: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * 商品情報を更新するサンプル
 */
function updateProduct($pdo, $productId, $updateData) {
    try {
        $setParts = [];
        $params = [':id' => $productId];
        
        foreach ($updateData as $field => $value) {
            $setParts[] = "{$field} = :{$field}";
            $params[":{$field}"] = $value;
        }
        
        $sql = "
            UPDATE products 
            SET " . implode(', ', $setParts) . ", updated_at = NOW()
            WHERE id = :id
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $affected = $stmt->rowCount();
        echo "✅ 商品更新成功: {$affected}件更新\n";
        return $affected > 0;
        
    } catch (PDOException $e) {
        echo "❌ 商品更新エラー: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * 商品を削除するサンプル（論理削除）
 */
function deleteProduct($pdo, $productId) {
    try {
        $sql = "UPDATE products SET is_active = FALSE WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $productId]);
        
        $affected = $stmt->rowCount();
        echo "✅ 商品削除成功: {$affected}件削除\n";
        return $affected > 0;
        
    } catch (PDOException $e) {
        echo "❌ 商品削除エラー: " . $e->getMessage() . "\n";
        return false;
    }
}

// ===== トランザクション処理例 =====

/**
 * 注文処理のトランザクション例
 */
function processOrder($pdo, $userId, $orderItems) {
    $pdo->beginTransaction();
    
    try {
        // 1. 注文レコード作成
        $orderSql = "
            INSERT INTO orders (
                user_id, order_number, status, subtotal, total_amount, created_at
            ) VALUES (
                :user_id, :order_number, 'pending', :subtotal, :total_amount, NOW()
            )
        ";
        
        $subtotal = array_sum(array_column($orderItems, 'total_price'));
        $orderNumber = 'ORD-' . date('Ymd') . '-' . sprintf('%03d', rand(1, 999));
        
        $stmt = $pdo->prepare($orderSql);
        $stmt->execute([
            ':user_id' => $userId,
            ':order_number' => $orderNumber,
            ':subtotal' => $subtotal,
            ':total_amount' => $subtotal
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // 2. 注文商品レコード作成 & 在庫更新
        $itemSql = "
            INSERT INTO order_items (
                order_id, product_id, product_name, product_sku,
                quantity, unit_price, total_price
            ) VALUES (
                :order_id, :product_id, :product_name, :product_sku,
                :quantity, :unit_price, :total_price
            )
        ";
        
        $stockSql = "
            UPDATE products 
            SET stock_quantity = stock_quantity - :quantity 
            WHERE id = :product_id AND stock_quantity >= :quantity
        ";
        
        $itemStmt = $pdo->prepare($itemSql);
        $stockStmt = $pdo->prepare($stockSql);
        
        foreach ($orderItems as $item) {
            // 注文商品追加
            $itemStmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $item['product_id'],
                ':product_name' => $item['product_name'],
                ':product_sku' => $item['product_sku'],
                ':quantity' => $item['quantity'],
                ':unit_price' => $item['unit_price'],
                ':total_price' => $item['total_price']
            ]);
            
            // 在庫更新
            $stockStmt->execute([
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity']
            ]);
            
            // 在庫不足チェック
            if ($stockStmt->rowCount() === 0) {
                throw new Exception("商品ID {$item['product_id']} の在庫が不足しています");
            }
        }
        
        $pdo->commit();
        echo "✅ 注文処理完了: 注文番号 = {$orderNumber}\n";
        return $orderId;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "❌ 注文処理エラー: " . $e->getMessage() . "\n";
        return false;
    }
}

// ===== 実行例 =====

function runExamples() {
    echo "=== MySQL接続サンプル実行 ===\n\n";
    
    // 1. 基本接続テスト
    echo "1. 基本接続テスト\n";
    $pdo = createAdvancedConnection();
    if (!$pdo) return;
    
    // 2. 商品データ取得
    echo "\n2. 商品データ取得\n";
    $products = getProducts($pdo, 5);
    foreach ($products as $product) {
        echo "  - {$product['name']} (¥{$product['price']}) [{$product['category_name']}]\n";
    }
    
    // 3. 新商品追加例
    echo "\n3. 新商品追加\n";
    $newProduct = [
        'category_id' => 2,
        'name' => 'サンプル商品',
        'slug' => 'sample-product-' . time(),
        'description' => 'これはサンプル商品です',
        'sku' => 'SAMPLE-' . time(),
        'price' => 1000.00,
        'stock_quantity' => 10,
        'is_featured' => false
    ];
    $newProductId = addProduct($pdo, $newProduct);
    
    // 4. 商品更新例
    if ($newProductId) {
        echo "\n4. 商品更新\n";
        updateProduct($pdo, $newProductId, [
            'price' => 1200.00,
            'stock_quantity' => 15
        ]);
    }
    
    // 5. Databaseクラス使用例
    echo "\n5. Databaseクラス使用例\n";
    try {
        $db = Database::getInstance();
        $connection = $db->getConnection();
        
        $stmt = $connection->query("SELECT COUNT(*) as count FROM products WHERE is_active = TRUE");
        $result = $stmt->fetch();
        echo "  アクティブな商品数: {$result['count']}件\n";
        
    } catch (Exception $e) {
        echo "  Databaseクラスエラー: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== サンプル実行完了 ===\n";
}

// コマンドライン実行時の処理
if (php_sapi_name() === 'cli') {
    runExamples();
}