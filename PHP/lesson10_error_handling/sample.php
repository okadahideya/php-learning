<?php

// エラーハンドリングの基本

echo "=== 基本的なtry-catch ===\n";

// 1. 基本的なエラー処理
try {
    $result = 10 / 0;
} catch (DivisionByZeroError $e) {
    echo "エラーをキャッチ: " . $e->getMessage() . "\n";
}

// 2. カスタム例外
function checkAge($age) {
    if (!is_numeric($age)) {
        throw new InvalidArgumentException("年齢は数値で入力してください");
    }
    if ($age < 0 || $age > 150) {
        throw new OutOfRangeException("年齢は0-150の範囲で入力してください");
    }
    return "年齢: {$age}歳";
}

$test_ages = [25, "abc", -5, 200];

foreach ($test_ages as $age) {
    try {
        echo checkAge($age) . "\n";
    } catch (InvalidArgumentException $e) {
        echo "引数エラー: " . $e->getMessage() . "\n";
    } catch (OutOfRangeException $e) {
        echo "範囲エラー: " . $e->getMessage() . "\n";
    }
}

echo "\n=== ファイル操作のエラー処理 ===\n";

// 3. ファイル操作エラー
function readFileContent($filename) {
    if (!file_exists($filename)) {
        throw new Exception("ファイルが存在しません: {$filename}");
    }
    
    $content = @file_get_contents($filename);
    if ($content === false) {
        throw new Exception("ファイル読み込みエラー: {$filename}");
    }
    
    return $content;
}

$files = ['sample.php', 'nonexistent.txt'];

foreach ($files as $file) {
    try {
        $content = readFileContent($file);
        echo "'{$file}' 読み込み成功 (" . strlen($content) . "バイト)\n";
    } catch (Exception $e) {
        echo "エラー: " . $e->getMessage() . "\n";
    }
}

echo "\n=== カスタム例外クラス ===\n";

// 4. カスタム例外クラス
class ValidationError extends Exception {
    private $field;
    
    public function __construct($message, $field = null) {
        parent::__construct($message);
        $this->field = $field;
    }
    
    public function getField() {
        return $this->field;
    }
}

function validateUser($data) {
    if (empty($data['name'])) {
        throw new ValidationError("名前は必須です", 'name');
    }
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new ValidationError("有効なメールアドレスが必要です", 'email');
    }
    return true;
}

$test_users = [
    ['name' => '田中太郎', 'email' => 'tanaka@example.com'],
    ['name' => '', 'email' => 'test@example.com'],
    ['name' => '佐藤花子', 'email' => 'invalid-email']
];

foreach ($test_users as $index => $user) {
    try {
        validateUser($user);
        echo "ユーザー" . ($index + 1) . ": 検証成功\n";
    } catch (ValidationError $e) {
        echo "ユーザー" . ($index + 1) . ": {$e->getField()}エラー - {$e->getMessage()}\n";
    }
}

echo "\n=== finally文の使用 ===\n";

// 5. finally文
function processData($data) {
    $start_time = microtime(true);
    $processed = 0;
    
    try {
        echo "データ処理開始\n";
        
        foreach ($data as $item) {
            if (!is_string($item)) {
                throw new InvalidArgumentException("文字列以外のデータ");
            }
            $processed++;
        }
        
        echo "すべて処理完了\n";
        
    } catch (InvalidArgumentException $e) {
        echo "エラー: " . $e->getMessage() . "\n";
        
    } finally {
        $elapsed = microtime(true) - $start_time;
        echo "処理時間: " . round($elapsed * 1000, 2) . "ms\n";
        echo "処理済み: {$processed}件\n";
    }
}

echo "正常データのテスト:\n";
processData(['apple', 'banana', 'cherry']);

echo "\n異常データのテスト:\n";
processData(['apple', 123, 'cherry']);

echo "\n=== エラーログ ===\n";

// 6. エラーログ記録
function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$message}\n";
    file_put_contents('error.log', $log_entry, FILE_APPEND);
    echo "エラーログに記録: {$message}\n";
}

try {
    throw new Exception("テストエラー");
} catch (Exception $e) {
    logError($e->getMessage());
}

?>