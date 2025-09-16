<?php
/**
 * Docker環境サンプルアプリケーション
 * PHP学習用のサンプルWebアプリケーション
 */

// エラー表示設定
error_reporting(E_ALL);
ini_set('display_errors', 1);

// セッション開始
session_start();

// データベース接続設定
class Database {
    private static $instance = null;
    private $connection = null;
    
    private function __construct() {
        $host = $_ENV['DB_HOST'] ?? 'database';
        $dbname = $_ENV['DB_DATABASE'] ?? 'php_learning';
        $username = $_ENV['DB_USERNAME'] ?? 'php_user';
        $password = $_ENV['DB_PASSWORD'] ?? 'php_password';
        
        try {
            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die('データベース接続エラー: ' . $e->getMessage());
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
}

// Redis接続設定
class RedisManager {
    private static $instance = null;
    private $redis = null;
    
    private function __construct() {
        $host = $_ENV['REDIS_HOST'] ?? 'redis';
        $port = $_ENV['REDIS_PORT'] ?? 6379;
        
        try {
            $this->redis = new Redis();
            $this->redis->connect($host, $port);
        } catch (Exception $e) {
            error_log('Redis接続エラー: ' . $e->getMessage());
            $this->redis = null;
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getRedis() {
        return $this->redis;
    }
    
    public function set($key, $value, $ttl = 3600) {
        if ($this->redis) {
            return $this->redis->setex($key, $ttl, serialize($value));
        }
        return false;
    }
    
    public function get($key) {
        if ($this->redis && $this->redis->exists($key)) {
            return unserialize($this->redis->get($key));
        }
        return null;
    }
}

// ルーティング処理
$request = $_GET['page'] ?? 'home';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docker環境サンプルアプリ | PHP学習</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- ナビゲーション -->
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">
                <i class="fab fa-docker mr-2"></i>Docker環境サンプル
            </h1>
            <div class="space-x-4">
                <a href="?page=home" class="hover:text-blue-200 transition <?= $request === 'home' ? 'border-b-2' : '' ?>">
                    <i class="fas fa-home mr-1"></i>ホーム
                </a>
                <a href="?page=database" class="hover:text-blue-200 transition <?= $request === 'database' ? 'border-b-2' : '' ?>">
                    <i class="fas fa-database mr-1"></i>データベース
                </a>
                <a href="?page=redis" class="hover:text-blue-200 transition <?= $request === 'redis' ? 'border-b-2' : '' ?>">
                    <i class="fas fa-memory mr-1"></i>Redis
                </a>
                <a href="?page=phpinfo" class="hover:text-blue-200 transition <?= $request === 'phpinfo' ? 'border-b-2' : '' ?>">
                    <i class="fab fa-php mr-1"></i>PHPInfo
                </a>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container mx-auto py-8 px-4">
        <?php
        switch ($request) {
            case 'home':
                ?>
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-rocket mr-2 text-blue-500"></i>
                            Docker環境サンプルアプリケーションへようこそ！
                        </h2>
                        <p class="text-gray-600 mb-4">
                            このアプリケーションは、Docker環境でのPHP開発を学習するためのサンプルです。
                            以下の技術スタックが含まれています：
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <i class="fas fa-server text-blue-500 text-2xl mb-2"></i>
                                <h3 class="font-semibold">Nginx</h3>
                                <p class="text-sm text-gray-600">Webサーバー</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg text-center">
                                <i class="fab fa-php text-purple-500 text-2xl mb-2"></i>
                                <h3 class="font-semibold">PHP 8.2</h3>
                                <p class="text-sm text-gray-600">アプリケーション</p>
                            </div>
                            <div class="bg-orange-50 p-4 rounded-lg text-center">
                                <i class="fas fa-database text-orange-500 text-2xl mb-2"></i>
                                <h3 class="font-semibold">MySQL 8.0</h3>
                                <p class="text-sm text-gray-600">データベース</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg text-center">
                                <i class="fas fa-memory text-red-500 text-2xl mb-2"></i>
                                <h3 class="font-semibold">Redis</h3>
                                <p class="text-sm text-gray-600">キャッシュ</p>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="font-semibold text-yellow-800 mb-2">
                                <i class="fas fa-info-circle mr-2"></i>アクセス情報
                            </h3>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• Webアプリケーション: <a href="http://localhost:8080" class="text-blue-600 hover:underline" target="_blank">http://localhost:8080</a></li>
                                <li>• phpMyAdmin: <a href="http://localhost:8081" class="text-blue-600 hover:underline" target="_blank">http://localhost:8081</a></li>
                                <li>• MailHog: <a href="http://localhost:8025" class="text-blue-600 hover:underline" target="_blank">http://localhost:8025</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- サーバー情報 -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-bold mb-4">
                            <i class="fas fa-info mr-2 text-green-500"></i>サーバー情報
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <strong>PHP バージョン:</strong> <?= PHP_VERSION ?><br>
                                <strong>サーバー時間:</strong> <?= date('Y-m-d H:i:s') ?><br>
                                <strong>タイムゾーン:</strong> <?= date_default_timezone_get() ?><br>
                                <strong>メモリ制限:</strong> <?= ini_get('memory_limit') ?>
                            </div>
                            <div>
                                <strong>拡張モジュール:</strong><br>
                                <span class="text-sm">
                                    <?php
                                    $extensions = ['pdo', 'mysqli', 'redis', 'gd', 'zip', 'mbstring'];
                                    foreach ($extensions as $ext) {
                                        $status = extension_loaded($ext) ? '✅' : '❌';
                                        echo "{$status} {$ext}<br>";
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                break;
                
            case 'database':
                ?>
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-database mr-2 text-orange-500"></i>データベース接続テスト
                        </h2>
                        
                        <?php
                        try {
                            $db = Database::getInstance();
                            $pdo = $db->getConnection();
                            
                            echo '<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">';
                            echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i>データベース接続成功！</p>';
                            echo '</div>';
                            
                            // テーブル一覧取得
                            $stmt = $pdo->query("SHOW TABLES");
                            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            
                            echo '<h3 class="font-bold mb-2">テーブル一覧:</h3>';
                            echo '<div class="bg-gray-50 rounded p-4 mb-4">';
                            foreach ($tables as $table) {
                                echo '<span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm mr-2 mb-2">' . $table . '</span>';
                            }
                            echo '</div>';
                            
                            // ユーザーデータ表示
                            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10");
                            $users = $stmt->fetchAll();
                            
                            if ($users) {
                                echo '<h3 class="font-bold mb-2">ユーザーデータ:</h3>';
                                echo '<div class="overflow-x-auto">';
                                echo '<table class="min-w-full bg-white border border-gray-200">';
                                echo '<thead class="bg-gray-50"><tr>';
                                echo '<th class="px-4 py-2 text-left">ID</th>';
                                echo '<th class="px-4 py-2 text-left">名前</th>';
                                echo '<th class="px-4 py-2 text-left">メール</th>';
                                echo '<th class="px-4 py-2 text-left">作成日時</th>';
                                echo '</tr></thead><tbody>';
                                
                                foreach ($users as $user) {
                                    echo '<tr class="border-t">';
                                    echo '<td class="px-4 py-2">' . $user['id'] . '</td>';
                                    echo '<td class="px-4 py-2">' . htmlspecialchars($user['name']) . '</td>';
                                    echo '<td class="px-4 py-2">' . htmlspecialchars($user['email']) . '</td>';
                                    echo '<td class="px-4 py-2">' . $user['created_at'] . '</td>';
                                    echo '</tr>';
                                }
                                
                                echo '</tbody></table></div>';
                            }
                            
                        } catch (Exception $e) {
                            echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
                            echo '<p class="text-red-800"><i class="fas fa-exclamation-circle mr-2"></i>データベース接続エラー: ' . $e->getMessage() . '</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
                <?php
                break;
                
            case 'redis':
                ?>
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-memory mr-2 text-red-500"></i>Redis接続テスト
                        </h2>
                        
                        <?php
                        $redisManager = RedisManager::getInstance();
                        $redis = $redisManager->getRedis();
                        
                        if ($redis) {
                            echo '<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">';
                            echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i>Redis接続成功！</p>';
                            echo '</div>';
                            
                            // Redis情報表示
                            $info = $redis->info();
                            echo '<h3 class="font-bold mb-2">Redis情報:</h3>';
                            echo '<div class="bg-gray-50 rounded p-4 mb-4">';
                            echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">';
                            echo '<div><strong>バージョン:</strong> ' . $info['redis_version'] . '</div>';
                            echo '<div><strong>稼働時間:</strong> ' . $info['uptime_in_seconds'] . '秒</div>';
                            echo '<div><strong>接続数:</strong> ' . $info['connected_clients'] . '</div>';
                            echo '<div><strong>使用メモリ:</strong> ' . round($info['used_memory'] / 1024 / 1024, 2) . 'MB</div>';
                            echo '</div></div>';
                            
                            // キャッシュテスト
                            $testKey = 'test_key_' . time();
                            $testValue = ['message' => 'Hello Redis!', 'timestamp' => time()];
                            
                            $redisManager->set($testKey, $testValue, 60);
                            $retrieved = $redisManager->get($testKey);
                            
                            echo '<h3 class="font-bold mb-2">キャッシュテスト:</h3>';
                            echo '<div class="bg-blue-50 rounded p-4">';
                            echo '<p><strong>保存したデータ:</strong> ' . json_encode($testValue, JSON_UNESCAPED_UNICODE) . '</p>';
                            echo '<p><strong>取得したデータ:</strong> ' . json_encode($retrieved, JSON_UNESCAPED_UNICODE) . '</p>';
                            echo '<p class="text-sm text-gray-600 mt-2">キー: ' . $testKey . ' (60秒でexpire)</p>';
                            echo '</div>';
                            
                        } else {
                            echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
                            echo '<p class="text-red-800"><i class="fas fa-exclamation-circle mr-2"></i>Redis接続エラー</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
                <?php
                break;
                
            case 'phpinfo':
                ?>
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">
                            <i class="fab fa-php mr-2 text-purple-500"></i>PHP情報
                        </h2>
                        <div class="bg-gray-50 rounded p-4">
                            <?php phpinfo(); ?>
                        </div>
                    </div>
                </div>
                <?php
                break;
                
            default:
                echo '<div class="text-center">';
                echo '<h2 class="text-2xl font-bold text-gray-800 mb-4">ページが見つかりません</h2>';
                echo '<a href="?page=home" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">';
                echo '<i class="fas fa-home mr-2"></i>ホームに戻る';
                echo '</a>';
                echo '</div>';
        }
        ?>
    </div>

    <!-- フッター -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-8">
        <p>&copy; 2024 PHP学習サンプル | Docker環境構築</p>
        <p class="text-sm text-gray-400 mt-1">
            <i class="fab fa-docker mr-1"></i>Docker Compose + 
            <i class="fas fa-server mr-1"></i>Nginx + 
            <i class="fab fa-php mr-1"></i>PHP + 
            <i class="fas fa-database mr-1"></i>MySQL + 
            <i class="fas fa-memory mr-1"></i>Redis
        </p>
    </footer>

    <script>
        // ページロード時のアニメーション
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.shadow-lg');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease-out';
                
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>