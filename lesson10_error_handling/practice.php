<?php

// 課題：以下の指示に従ってエラーハンドリングを実装してください

echo "=== エラーハンドリング課題 ===\n";

// 1. 割り算を行う関数を作成し、ゼロ除算エラーをキャッチしてください
function safeDivide($a, $b) {
    // ここに実装してください
    
}

echo "1. 割り算のテスト:\n";
$test_cases = [[10, 2], [15, 3], [20, 0], [100, 4]];
// テストを実行してください


// 2. 配列の要素にアクセスする関数を作成し、存在しないインデックスのエラーを処理してください
function getArrayElement($array, $index) {
    // ここに実装してください（存在しないインデックスの場合は例外をthrow）
    
}

echo "\n2. 配列アクセスのテスト:\n";
$test_array = ['apple', 'banana', 'cherry'];
$test_indexes = [0, 1, 5, 2];
// テストを実行してください


// 3. ファイル書き込み関数を作成し、ファイル操作のエラーを処理してください
function writeToFile($filename, $content) {
    // ここに実装してください
    
}

echo "\n3. ファイル書き込みのテスト:\n";
$test_files = [
    ['test1.txt', 'Hello World'],
    ['/invalid/path/test.txt', 'This will fail'],
    ['test2.txt', 'PHP Error Handling']
];
// テストを実行してください


// 4. カスタム例外クラス「EmailException」を作成してください
class EmailException extends Exception {
    // ここに実装してください
    
}

// 5. メール送信をシミュレートする関数を作成してください
function sendEmail($to, $subject, $body) {
    // バリデーション処理を実装してください
    // - $toが空の場合: EmailException
    // - メールアドレス形式が無効の場合: EmailException  
    // - $subjectが空の場合: EmailException
    
}

echo "\n4-5. メール送信のテスト:\n";
$test_emails = [
    ['test@example.com', 'テストメール', 'こんにちは'],
    ['', 'テスト', '空のメールアドレス'],
    ['invalid-email', 'テスト', '無効な形式'],
    ['test@example.com', '', 'タイトルなし']
];
// テストを実行してください


// 6. 複数の処理を含む関数で、finallyブロックを使用してください
function processMultipleFiles($filenames) {
    $processed_count = 0;
    $start_time = microtime(true);
    
    try {
        echo "ファイル処理開始\n";
        
        // ここに各ファイルの処理を実装してください
        // ファイルが存在しない場合は例外を発生させる
        
    } catch (Exception $e) {
        // エラー処理を実装してください
        
    } finally {
        // 必ず実行される処理を実装してください
        // 処理時間、処理済みファイル数を表示
        
    }
}

echo "\n6. 複数ファイル処理のテスト:\n";
// sample.php, nonexistent.txt, practice.php でテストしてください


// 7. エラーログ記録関数を作成してください
function recordError($message, $severity = 'ERROR', $context = []) {
    // ここに実装してください
    // フォーマット: [日時] [重要度] メッセージ | Context: JSON
    
}

echo "\n7. エラーログのテスト:\n";
// 複数のエラーレベルでログを記録してください


echo "\n=== 課題完了 ===\n";

?>