-- ===== MySQL実用クエリサンプル集 =====
-- 実際の業務で使用される高度なSQLクエリの例

-- ===== 基本的なCRUD操作 =====

-- 1. 商品検索（複数条件）
SELECT 
    p.id,
    p.name,
    p.price,
    p.sale_price,
    p.stock_quantity,
    c.name as category_name
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.is_active = TRUE
  AND p.stock_quantity > 0
  AND (p.sale_price IS NOT NULL OR p.price BETWEEN 1000 AND 50000)
ORDER BY p.is_featured DESC, p.created_at DESC
LIMIT 20;

-- 2. ユーザーの注文履歴
SELECT 
    o.order_number,
    o.status,
    o.total_amount,
    o.created_at,
    COUNT(oi.id) as item_count
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE o.user_id = 1
GROUP BY o.id
ORDER BY o.created_at DESC;

-- 3. カートの中身と合計金額
SELECT 
    p.name,
    p.price,
    p.sale_price,
    ci.quantity,
    CASE 
        WHEN p.sale_price IS NOT NULL THEN p.sale_price * ci.quantity
        ELSE p.price * ci.quantity
    END as subtotal
FROM cart_items ci
JOIN products p ON ci.product_id = p.id
WHERE ci.user_id = 2;

-- カート合計金額
SELECT 
    SUM(
        CASE 
            WHEN p.sale_price IS NOT NULL THEN p.sale_price * ci.quantity
            ELSE p.price * ci.quantity
        END
    ) as cart_total
FROM cart_items ci
JOIN products p ON ci.product_id = p.id
WHERE ci.user_id = 2;

-- ===== 集計・分析クエリ =====

-- 4. 月別売上集計
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as order_count,
    SUM(total_amount) as total_sales,
    AVG(total_amount) as avg_order_value,
    MIN(total_amount) as min_order,
    MAX(total_amount) as max_order
FROM orders
WHERE status IN ('shipped', 'delivered')
  AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;

-- 5. 商品別売上ランキング
SELECT 
    p.name,
    p.sku,
    SUM(oi.quantity) as total_sold,
    SUM(oi.total_price) as total_revenue,
    AVG(oi.unit_price) as avg_price,
    COUNT(DISTINCT oi.order_id) as order_count
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN orders o ON oi.order_id = o.id
WHERE o.status IN ('shipped', 'delivered')
  AND o.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
GROUP BY p.id, p.name, p.sku
ORDER BY total_revenue DESC
LIMIT 10;

-- 6. カテゴリ別売上分析
SELECT 
    c.name as category,
    COUNT(DISTINCT p.id) as product_count,
    SUM(oi.quantity) as items_sold,
    SUM(oi.total_price) as category_revenue,
    AVG(oi.unit_price) as avg_item_price
FROM categories c
JOIN products p ON c.id = p.category_id
JOIN order_items oi ON p.id = oi.product_id
JOIN orders o ON oi.order_id = o.id
WHERE o.status IN ('shipped', 'delivered')
  AND o.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY c.id, c.name
ORDER BY category_revenue DESC;

-- 7. 顧客分析（RFM分析の基礎）
SELECT 
    u.id,
    CONCAT(u.last_name, ' ', u.first_name) as customer_name,
    u.email,
    COUNT(o.id) as order_frequency,  -- Frequency
    MAX(o.created_at) as last_order_date,  -- Recency
    DATEDIFF(NOW(), MAX(o.created_at)) as days_since_last_order,
    SUM(o.total_amount) as total_spent,  -- Monetary
    AVG(o.total_amount) as avg_order_value
FROM users u
LEFT JOIN orders o ON u.id = o.user_id AND o.status IN ('shipped', 'delivered')
WHERE u.is_active = TRUE
GROUP BY u.id, u.last_name, u.first_name, u.email
HAVING COUNT(o.id) > 0
ORDER BY total_spent DESC;

-- ===== 高度なJOINクエリ =====

-- 8. 在庫切れ商品の影響分析
SELECT 
    p.name,
    p.sku,
    p.stock_quantity,
    c.name as category,
    COUNT(ci.id) as cart_waiting_count,
    SUM(ci.quantity) as total_waiting_quantity,
    p.price * SUM(ci.quantity) as potential_lost_sales
FROM products p
JOIN categories c ON p.category_id = c.id
LEFT JOIN cart_items ci ON p.id = ci.product_id
WHERE p.stock_quantity = 0
  AND p.is_active = TRUE
GROUP BY p.id, p.name, p.sku, p.stock_quantity, c.name, p.price
HAVING cart_waiting_count > 0
ORDER BY potential_lost_sales DESC;

-- 9. 地域別配送分析
SELECT 
    shipping_state as prefecture,
    shipping_city as city,
    COUNT(*) as order_count,
    SUM(total_amount) as total_sales,
    AVG(total_amount) as avg_order_value,
    AVG(shipping_amount) as avg_shipping_cost
FROM orders
WHERE status IN ('shipped', 'delivered')
  AND shipping_state IS NOT NULL
  AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY shipping_state, shipping_city
HAVING order_count >= 2
ORDER BY total_sales DESC;

-- 10. 商品の価格変動分析（セール効果）
SELECT 
    p.name,
    p.price as regular_price,
    p.sale_price,
    ROUND(((p.price - p.sale_price) / p.price) * 100, 2) as discount_rate,
    COUNT(oi.id) as orders_during_sale,
    SUM(oi.quantity) as items_sold,
    SUM(oi.total_price) as sale_revenue,
    (p.price - p.sale_price) * SUM(oi.quantity) as discount_amount
FROM products p
JOIN order_items oi ON p.id = oi.product_id
JOIN orders o ON oi.order_id = o.id
WHERE p.sale_price IS NOT NULL
  AND o.status IN ('shipped', 'delivered')
  AND o.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
GROUP BY p.id, p.name, p.price, p.sale_price
ORDER BY sale_revenue DESC;

-- ===== サブクエリの活用 =====

-- 11. 平均注文金額より高い注文
SELECT 
    o.order_number,
    CONCAT(u.last_name, ' ', u.first_name) as customer,
    o.total_amount,
    o.created_at
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.total_amount > (
    SELECT AVG(total_amount) 
    FROM orders 
    WHERE status IN ('shipped', 'delivered')
)
  AND o.status IN ('shipped', 'delivered')
ORDER BY o.total_amount DESC;

-- 12. 各カテゴリで最も売れている商品
SELECT 
    c.name as category,
    p.name as best_seller,
    sales.total_sold,
    sales.total_revenue
FROM categories c
JOIN (
    SELECT 
        p.category_id,
        p.name,
        SUM(oi.quantity) as total_sold,
        SUM(oi.total_price) as total_revenue,
        ROW_NUMBER() OVER (PARTITION BY p.category_id ORDER BY SUM(oi.quantity) DESC) as rank_in_category
    FROM products p
    JOIN order_items oi ON p.id = oi.product_id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status IN ('shipped', 'delivered')
    GROUP BY p.id, p.category_id, p.name
) sales ON c.id = sales.category_id
JOIN products p ON sales.category_id = p.category_id AND sales.name = p.name
WHERE sales.rank_in_category = 1
ORDER BY sales.total_revenue DESC;

-- 13. 新規顧客 vs リピート顧客の分析
SELECT 
    customer_type,
    COUNT(*) as customer_count,
    SUM(total_amount) as total_revenue,
    AVG(total_amount) as avg_order_value
FROM (
    SELECT 
        u.id,
        o.total_amount,
        CASE 
            WHEN COUNT(prev_orders.id) = 0 THEN '新規顧客'
            ELSE 'リピート顧客'
        END as customer_type
    FROM users u
    JOIN orders o ON u.id = o.user_id
    LEFT JOIN orders prev_orders ON u.id = prev_orders.user_id 
        AND prev_orders.created_at < o.created_at
        AND prev_orders.status IN ('shipped', 'delivered')
    WHERE o.status IN ('shipped', 'delivered')
      AND o.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
    GROUP BY u.id, o.id, o.total_amount
) customer_analysis
GROUP BY customer_type;

-- ===== ウィンドウ関数の活用 =====

-- 14. 売上の移動平均（7日間）
SELECT 
    date(created_at) as order_date,
    COUNT(*) as daily_orders,
    SUM(total_amount) as daily_sales,
    AVG(SUM(total_amount)) OVER (
        ORDER BY date(created_at) 
        ROWS BETWEEN 6 PRECEDING AND CURRENT ROW
    ) as moving_avg_7days
FROM orders
WHERE status IN ('shipped', 'delivered')
  AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY date(created_at)
ORDER BY order_date;

-- 15. 商品の売上ランキング（カテゴリ内順位）
SELECT 
    c.name as category,
    p.name as product,
    SUM(oi.total_price) as revenue,
    RANK() OVER (PARTITION BY c.id ORDER BY SUM(oi.total_price) DESC) as category_rank,
    RANK() OVER (ORDER BY SUM(oi.total_price) DESC) as overall_rank
FROM products p
JOIN categories c ON p.category_id = c.id
JOIN order_items oi ON p.id = oi.product_id
JOIN orders o ON oi.order_id = o.id
WHERE o.status IN ('shipped', 'delivered')
  AND o.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
GROUP BY c.id, c.name, p.id, p.name
ORDER BY overall_rank;

-- ===== パフォーマンス最適化クエリ =====

-- 16. インデックス効果の確認
EXPLAIN FORMAT=JSON
SELECT p.*, c.name as category_name
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.is_active = TRUE
  AND p.is_featured = TRUE
  AND c.is_active = TRUE
ORDER BY p.created_at DESC
LIMIT 10;

-- 17. 重複データの検出
SELECT 
    email,
    COUNT(*) as duplicate_count
FROM users
GROUP BY email
HAVING COUNT(*) > 1;

-- 18. テーブルサイズとインデックス使用量
SELECT 
    TABLE_NAME,
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as 'Size (MB)',
    ROUND((DATA_LENGTH / 1024 / 1024), 2) as 'Data (MB)',
    ROUND((INDEX_LENGTH / 1024 / 1024), 2) as 'Index (MB)',
    TABLE_ROWS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'ecommerce_sample'
  AND TABLE_TYPE = 'BASE TABLE'
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

-- ===== データメンテナンス用クエリ =====

-- 19. 古いカートアイテムの削除
DELETE FROM cart_items
WHERE updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- 20. 在庫更新（注文処理時）
UPDATE products 
SET stock_quantity = stock_quantity - (
    SELECT SUM(oi.quantity)
    FROM order_items oi
    WHERE oi.product_id = products.id
      AND oi.order_id = 1  -- 特定の注文ID
)
WHERE id IN (
    SELECT DISTINCT product_id
    FROM order_items
    WHERE order_id = 1
);

-- 21. 売上レポート用ビューのリフレッシュ
-- （ビューは自動更新されるため、実際には不要だが、マテリアライズドビューがある場合の例）
-- REFRESH MATERIALIZED VIEW monthly_sales_report;

-- ===== セキュリティ関連クエリ =====

-- 22. 異常な注文パターンの検出
SELECT 
    u.email,
    COUNT(o.id) as order_count_today,
    SUM(o.total_amount) as total_amount_today
FROM users u
JOIN orders o ON u.id = o.user_id
WHERE DATE(o.created_at) = CURDATE()
GROUP BY u.id, u.email
HAVING order_count_today > 5 OR total_amount_today > 500000
ORDER BY total_amount_today DESC;

-- 23. 未認証ユーザーの注文
SELECT 
    u.email,
    u.created_at as user_created,
    COUNT(o.id) as order_count,
    SUM(o.total_amount) as total_spent
FROM users u
JOIN orders o ON u.id = o.user_id
WHERE u.email_verified_at IS NULL
  AND o.status IN ('shipped', 'delivered')
GROUP BY u.id, u.email, u.created_at
ORDER BY total_spent DESC;

-- ===== バックアップ・復旧関連 =====

-- 24. 特定期間のデータエクスポート
SELECT 
    'orders' as table_name,
    COUNT(*) as record_count,
    MIN(created_at) as earliest_record,
    MAX(created_at) as latest_record
FROM orders
WHERE created_at >= '2024-01-01'
  AND created_at < '2024-02-01'

UNION ALL

SELECT 
    'order_items' as table_name,
    COUNT(*) as record_count,
    MIN(oi.created_at) as earliest_record,
    MAX(oi.created_at) as latest_record
FROM order_items oi
JOIN orders o ON oi.order_id = o.id
WHERE o.created_at >= '2024-01-01'
  AND o.created_at < '2024-02-01';

-- 25. データ整合性チェック
SELECT 
    'orphaned_order_items' as issue_type,
    COUNT(*) as issue_count
FROM order_items oi
LEFT JOIN orders o ON oi.order_id = o.id
WHERE o.id IS NULL

UNION ALL

SELECT 
    'products_without_category' as issue_type,
    COUNT(*) as issue_count
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE c.id IS NULL

UNION ALL

SELECT 
    'orders_without_items' as issue_type,
    COUNT(*) as issue_count
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE oi.id IS NULL;

-- ===== 便利なユーティリティクエリ =====

-- 26. 今日の売上サマリー
SELECT 
    COUNT(DISTINCT o.id) as orders_today,
    COUNT(oi.id) as items_sold,
    SUM(o.total_amount) as revenue_today,
    AVG(o.total_amount) as avg_order_value
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE DATE(o.created_at) = CURDATE()
  AND o.status IN ('shipped', 'delivered', 'processing');

-- 27. 在庫アラート
SELECT 
    p.name,
    p.sku,
    p.stock_quantity,
    c.name as category,
    CASE 
        WHEN p.stock_quantity = 0 THEN '在庫切れ'
        WHEN p.stock_quantity <= 5 THEN '在庫少'
        ELSE '在庫あり'
    END as stock_status
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.is_active = TRUE
  AND p.manage_stock = TRUE
  AND p.stock_quantity <= 5
ORDER BY p.stock_quantity ASC, p.name;

-- 28. 顧客の最終ログイン情報
SELECT 
    CONCAT(u.last_name, ' ', u.first_name) as customer_name,
    u.email,
    u.last_login_at,
    DATEDIFF(NOW(), u.last_login_at) as days_since_login,
    COUNT(o.id) as total_orders,
    MAX(o.created_at) as last_order_date
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
WHERE u.is_active = TRUE
GROUP BY u.id, u.last_name, u.first_name, u.email, u.last_login_at
ORDER BY days_since_login DESC
LIMIT 20;