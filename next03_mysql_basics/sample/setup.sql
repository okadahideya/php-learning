-- ===== MySQL実用学習サンプル - ECサイトデータベース =====
-- 実際のECサイトを想定したデータベース設計のサンプル

-- データベース作成
CREATE DATABASE IF NOT EXISTS ecommerce_sample CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_sample;

-- 既存テーブル削除（リセット用）
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS user_addresses;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS admin_users;

-- ===== カテゴリテーブル =====
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    parent_id INT,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- インデックス
    INDEX idx_parent_id (parent_id),
    INDEX idx_slug (slug),
    INDEX idx_sort_order (sort_order),
    
    -- 外部キー制約
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ===== 商品テーブル =====
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    sku VARCHAR(100) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    stock_quantity INT DEFAULT 0,
    manage_stock BOOLEAN DEFAULT TRUE,
    weight DECIMAL(8,2),
    dimensions VARCHAR(100),
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- インデックス
    INDEX idx_category_id (category_id),
    INDEX idx_slug (slug),
    INDEX idx_sku (sku),
    INDEX idx_price (price),
    INDEX idx_stock (stock_quantity),
    INDEX idx_featured (is_featured),
    INDEX idx_active (is_active),
    INDEX idx_created_at (created_at),
    
    -- 複合インデックス
    INDEX idx_category_active (category_id, is_active),
    INDEX idx_featured_active (is_featured, is_active),
    INDEX idx_price_active (price, is_active),
    
    -- 外部キー制約
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- ===== 商品画像テーブル =====
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- インデックス
    INDEX idx_product_id (product_id),
    INDEX idx_sort_order (sort_order),
    INDEX idx_primary (is_primary),
    
    -- 外部キー制約
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ===== ユーザーテーブル =====
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    birth_date DATE,
    gender ENUM('male', 'female', 'other'),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- インデックス
    INDEX idx_email (email),
    INDEX idx_name (last_name, first_name),
    INDEX idx_active (is_active),
    INDEX idx_created_at (created_at)
);

-- ===== ユーザー住所テーブル =====
CREATE TABLE user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('billing', 'shipping') NOT NULL DEFAULT 'shipping',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    address_1 VARCHAR(255) NOT NULL,
    address_2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'Japan',
    phone VARCHAR(20),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- インデックス
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_default (is_default),
    
    -- 外部キー制約
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===== 注文テーブル =====
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    currency VARCHAR(3) DEFAULT 'JPY',
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    shipping_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    
    -- 請求先住所
    billing_first_name VARCHAR(100),
    billing_last_name VARCHAR(100),
    billing_company VARCHAR(100),
    billing_address_1 VARCHAR(255),
    billing_address_2 VARCHAR(255),
    billing_city VARCHAR(100),
    billing_state VARCHAR(100),
    billing_postal_code VARCHAR(20),
    billing_country VARCHAR(100),
    billing_phone VARCHAR(20),
    
    -- 配送先住所
    shipping_first_name VARCHAR(100),
    shipping_last_name VARCHAR(100),
    shipping_company VARCHAR(100),
    shipping_address_1 VARCHAR(255),
    shipping_address_2 VARCHAR(255),
    shipping_city VARCHAR(100),
    shipping_state VARCHAR(100),
    shipping_postal_code VARCHAR(20),
    shipping_country VARCHAR(100),
    shipping_phone VARCHAR(20),
    
    notes TEXT,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- インデックス
    INDEX idx_user_id (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at),
    INDEX idx_total_amount (total_amount),
    
    -- 複合インデックス
    INDEX idx_user_status (user_id, status),
    INDEX idx_status_created (status, created_at),
    
    -- 外部キー制約
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- ===== 注文商品テーブル =====
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL, -- 注文時点の商品名保存
    product_sku VARCHAR(100) NOT NULL,  -- 注文時点のSKU保存
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,  -- 注文時点の価格保存
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- インデックス
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id),
    
    -- 外部キー制約
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- ===== カートテーブル =====
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- ユニーク制約（ユーザー毎に同じ商品は1レコードのみ）
    UNIQUE KEY unique_user_product (user_id, product_id),
    
    -- インデックス
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id),
    
    -- 外部キー制約
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ===== 管理者ユーザーテーブル =====
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'manager', 'staff') DEFAULT 'staff',
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- インデックス
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
);

-- ===== サンプルデータ投入 =====

-- カテゴリデータ
INSERT INTO categories (name, slug, description, parent_id, sort_order) VALUES
('エレクトロニクス', 'electronics', '電子機器・家電製品', NULL, 1),
('コンピュータ', 'computers', 'PC・ノートパソコン・タブレット', 1, 1),
('スマートフォン', 'smartphones', 'スマートフォン・携帯電話', 1, 2),
('家電', 'appliances', '生活家電', 1, 3),
('ファッション', 'fashion', '衣類・アクセサリー', NULL, 2),
('メンズ', 'mens', '男性向けファッション', 5, 1),
('レディース', 'womens', '女性向けファッション', 5, 2),
('本・メディア', 'books-media', '書籍・DVD・音楽', NULL, 3),
('書籍', 'books', '本・雑誌', 8, 1),
('DVD・ブルーレイ', 'dvd-bluray', '映像メディア', 8, 2);

-- 商品データ
INSERT INTO products (category_id, name, slug, description, short_description, sku, price, sale_price, stock_quantity, weight, is_featured) VALUES
(2, 'MacBook Pro 14インチ', 'macbook-pro-14', 'Apple M2 Pro チップ搭載の高性能ノートパソコン。プロフェッショナル向けの作業に最適です。', 'M2 Pro搭載の高性能ノートPC', 'MBP-14-M2P-512', 298000.00, 278000.00, 15, 1.60, TRUE),
(2, 'Dell XPS 13', 'dell-xps-13', 'コンパクトで軽量なプレミアムノートパソコン。ビジネス用途に最適です。', '軽量プレミアムノートPC', 'DELL-XPS13-2023', 149800.00, NULL, 25, 1.20, FALSE),
(3, 'iPhone 15 Pro', 'iphone-15-pro', '最新のA17 Proチップを搭載したAppleの最高峰スマートフォン。', 'A17 Pro搭載の最新iPhone', 'IPH15-PRO-256-BL', 159800.00, 149800.00, 50, 0.19, TRUE),
(3, 'Google Pixel 8', 'google-pixel-8', 'Google純正のAndroidスマートフォン。AI機能が充実。', 'AI機能搭載のGoogle純正スマホ', 'PIX8-128-GR', 89800.00, NULL, 30, 0.19, FALSE),
(4, 'Dyson V15 Detect', 'dyson-v15-detect', 'レーザーでゴミを可視化するコードレス掃除機。', 'レーザー搭載コードレス掃除機', 'DYS-V15-DET', 89800.00, 79800.00, 20, 3.20, TRUE),
(6, 'ユニクロ ヒートテック Tシャツ', 'uniqlo-heattech-tshirt', '保温性に優れた機能性インナー。', '暖かい機能性インナー', 'UNI-HT-TS-M-BK', 1290.00, 990.00, 100, 0.15, FALSE),
(7, 'ZARA ニットワンピース', 'zara-knit-dress', 'エレガントなニットワンピース。', '上品なニットワンピース', 'ZAR-KD-M-NV', 7990.00, NULL, 40, 0.40, FALSE),
(9, 'JavaScript入門書', 'javascript-beginners-book', 'プログラミング初心者向けのJavaScript解説書。', 'JS初心者向け解説書', 'JS-BEG-BOOK-2023', 2980.00, NULL, 75, 0.35, FALSE),
(10, 'アベンジャーズ 4K UHD', 'avengers-4k-uhd', 'マーベル・アベンジャーズの4K Ultra HD版。', 'アベンジャーズ4K版', 'AVG-4K-UHD-JP', 3980.00, 2980.00, 60, 0.08, FALSE);

-- 商品画像データ
INSERT INTO product_images (product_id, filename, alt_text, sort_order, is_primary) VALUES
(1, 'macbook-pro-14-main.jpg', 'MacBook Pro 14インチ メイン画像', 1, TRUE),
(1, 'macbook-pro-14-side.jpg', 'MacBook Pro 14インチ サイド画像', 2, FALSE),
(2, 'dell-xps-13-main.jpg', 'Dell XPS 13 メイン画像', 1, TRUE),
(3, 'iphone-15-pro-main.jpg', 'iPhone 15 Pro メイン画像', 1, TRUE),
(3, 'iphone-15-pro-back.jpg', 'iPhone 15 Pro 背面画像', 2, FALSE),
(4, 'pixel-8-main.jpg', 'Google Pixel 8 メイン画像', 1, TRUE),
(5, 'dyson-v15-main.jpg', 'Dyson V15 Detect メイン画像', 1, TRUE),
(6, 'heattech-tshirt-main.jpg', 'ヒートテック Tシャツ メイン画像', 1, TRUE),
(7, 'zara-dress-main.jpg', 'ZARA ニットワンピース メイン画像', 1, TRUE),
(8, 'js-book-cover.jpg', 'JavaScript入門書 表紙', 1, TRUE),
(9, 'avengers-4k-cover.jpg', 'アベンジャーズ 4K UHD パッケージ', 1, TRUE);

-- ユーザーデータ
INSERT INTO users (email, password, first_name, last_name, phone, birth_date, gender, email_verified_at) VALUES
('tanaka@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '太郎', '田中', '090-1234-5678', '1985-03-15', 'male', NOW()),
('sato@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '花子', '佐藤', '090-2345-6789', '1990-07-22', 'female', NOW()),
('yamada@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '次郎', '山田', '090-3456-7890', '1988-11-10', 'male', NOW()),
('suzuki@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '美咲', '鈴木', '090-4567-8901', '1992-05-18', 'female', NULL),
('watanabe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '孝', '渡辺', NULL, '1987-09-30', 'male', NOW());

-- ユーザー住所データ
INSERT INTO user_addresses (user_id, type, first_name, last_name, address_1, address_2, city, state, postal_code, phone, is_default) VALUES
(1, 'shipping', '太郎', '田中', '港区六本木1-1-1', 'タワーマンション101', '東京都', '東京都', '106-0032', '090-1234-5678', TRUE),
(1, 'billing', '太郎', '田中', '港区六本木1-1-1', 'タワーマンション101', '東京都', '東京都', '106-0032', '090-1234-5678', TRUE),
(2, 'shipping', '花子', '佐藤', '渋谷区原宿2-2-2', 'アパート202', '東京都', '東京都', '150-0001', '090-2345-6789', TRUE),
(3, 'shipping', '次郎', '山田', '大阪市北区梅田3-3-3', 'マンション303', '大阪府', '大阪府', '530-0001', '090-3456-7890', TRUE);

-- 注文データ
INSERT INTO orders (user_id, order_number, status, subtotal, tax_amount, shipping_amount, total_amount, payment_method, payment_status, 
                   billing_first_name, billing_last_name, billing_address_1, billing_city, billing_state, billing_postal_code, billing_phone,
                   shipping_first_name, shipping_last_name, shipping_address_1, shipping_city, shipping_state, shipping_postal_code, shipping_phone,
                   created_at) VALUES
(1, 'ORD-20240115-001', 'delivered', 278000.00, 27800.00, 0.00, 305800.00, 'credit_card', 'paid',
 '太郎', '田中', '港区六本木1-1-1', '東京都', '東京都', '106-0032', '090-1234-5678',
 '太郎', '田中', '港区六本木1-1-1', '東京都', '東京都', '106-0032', '090-1234-5678',
 '2024-01-15 10:30:00'),
(2, 'ORD-20240116-002', 'shipped', 91770.00, 9177.00, 500.00, 101447.00, 'credit_card', 'paid',
 '花子', '佐藤', '渋谷区原宿2-2-2', '東京都', '東京都', '150-0001', '090-2345-6789',
 '花子', '佐藤', '渋谷区原宿2-2-2', '東京都', '東京都', '150-0001', '090-2345-6789',
 '2024-01-16 14:20:00'),
(1, 'ORD-20240118-003', 'processing', 82770.00, 8277.00, 500.00, 91547.00, 'bank_transfer', 'paid',
 '太郎', '田中', '港区六本木1-1-1', '東京都', '東京都', '106-0032', '090-1234-5678',
 '太郎', '田中', '港区六本木1-1-1', '東京都', '東京都', '106-0032', '090-1234-5678',
 '2024-01-18 16:45:00'),
(3, 'ORD-20240120-004', 'pending', 5960.00, 596.00, 300.00, 6856.00, 'cash_on_delivery', 'pending',
 '次郎', '山田', '大阪市北区梅田3-3-3', '大阪府', '大阪府', '530-0001', '090-3456-7890',
 '次郎', '山田', '大阪市北区梅田3-3-3', '大阪府', '大阪府', '530-0001', '090-3456-7890',
 '2024-01-20 11:15:00');

-- 注文商品データ
INSERT INTO order_items (order_id, product_id, product_name, product_sku, quantity, unit_price, total_price) VALUES
(1, 1, 'MacBook Pro 14インチ', 'MBP-14-M2P-512', 1, 278000.00, 278000.00),
(2, 3, 'iPhone 15 Pro', 'IPH15-PRO-256-BL', 1, 149800.00, 149800.00),
(2, 6, 'ユニクロ ヒートテック Tシャツ', 'UNI-HT-TS-M-BK', 2, 990.00, 1980.00),
(3, 5, 'Dyson V15 Detect', 'DYS-V15-DET', 1, 79800.00, 79800.00),
(3, 8, 'JavaScript入門書', 'JS-BEG-BOOK-2023', 1, 2980.00, 2980.00),
(4, 9, 'アベンジャーズ 4K UHD', 'AVG-4K-UHD-JP', 2, 2980.00, 5960.00);

-- カートデータ
INSERT INTO cart_items (user_id, product_id, quantity) VALUES
(2, 2, 1),  -- 佐藤さん: Dell XPS 13
(2, 7, 1),  -- 佐藤さん: ZARA ニットワンピース
(4, 4, 1),  -- 鈴木さん: Google Pixel 8
(4, 8, 2);  -- 鈴木さん: JavaScript入門書 x2

-- 管理者ユーザーデータ
INSERT INTO admin_users (username, email, password, first_name, last_name, role) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '管理', '太郎', 'super_admin'),
('manager', 'manager@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '管理', '花子', 'manager'),
('staff', 'staff@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'スタッフ', '次郎', 'staff');

-- ===== ビューの作成 =====

-- 商品一覧ビュー（カテゴリ情報含む）
CREATE VIEW product_list_view AS
SELECT 
    p.id,
    p.name,
    p.slug,
    p.short_description,
    p.sku,
    CASE 
        WHEN p.sale_price IS NOT NULL THEN p.sale_price 
        ELSE p.price 
    END as current_price,
    p.price as original_price,
    p.sale_price,
    p.stock_quantity,
    p.is_featured,
    p.is_active,
    c.name as category_name,
    c.slug as category_slug,
    pi.filename as primary_image,
    p.created_at
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
WHERE p.is_active = TRUE
ORDER BY p.is_featured DESC, p.created_at DESC;

-- 注文詳細ビュー
CREATE VIEW order_details_view AS
SELECT 
    o.id as order_id,
    o.order_number,
    o.status,
    o.total_amount,
    o.payment_status,
    o.created_at as order_date,
    u.first_name,
    u.last_name,
    u.email,
    GROUP_CONCAT(
        CONCAT(oi.product_name, ' x', oi.quantity) 
        ORDER BY oi.id 
        SEPARATOR ', '
    ) as items_summary,
    COUNT(oi.id) as items_count
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id, o.order_number, o.status, o.total_amount, o.payment_status, o.created_at, u.first_name, u.last_name, u.email
ORDER BY o.created_at DESC;

-- ===== パフォーマンス分析用クエリの例 =====

-- インデックス使用状況確認
-- EXPLAIN SELECT * FROM products WHERE category_id = 1 AND is_active = TRUE;

-- スロークエリ用の複雑なクエリ例
-- SELECT p.*, c.name as category_name, AVG(r.rating) as avg_rating
-- FROM products p 
-- LEFT JOIN categories c ON p.category_id = c.id 
-- LEFT JOIN reviews r ON p.id = r.product_id 
-- WHERE p.is_active = TRUE 
-- GROUP BY p.id 
-- ORDER BY avg_rating DESC;

COMMIT;