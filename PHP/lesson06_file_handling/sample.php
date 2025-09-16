<?php

// ファイル操作の基本

echo "=== ファイルの読み書き ===\n";

// ファイルに書き込み
$filename = "test.txt";
$content = "こんにちは、PHPの世界！\n";

file_put_contents($filename, $content);
echo "ファイルを作成しました。\n";

// ファイル読み込み
$file_content = file_get_contents($filename);
echo "ファイルの内容: " . trim($file_content) . "\n";

// 追記
file_put_contents($filename, "追記しました。\n", FILE_APPEND);
echo "追記完了。\n";

echo "\n=== CSVファイル ===\n";

// CSV作成
$csv_data = [
    ['名前', '年齢'],
    ['田中太郎', '25'],
    ['佐藤花子', '30']
];

$csv_file = "users.csv";
$handle = fopen($csv_file, 'w');
foreach ($csv_data as $row) {
    fputcsv($handle, $row);
}
fclose($handle);
echo "CSVファイルを作成しました。\n";

// CSV読み込み
$handle = fopen($csv_file, 'r');
while (($row = fgetcsv($handle)) !== false) {
    echo implode(' | ', $row) . "\n";
}
fclose($handle);

echo "\n=== JSONファイル ===\n";

// JSON保存
$data = [
    'users' => [
        ['name' => '山田太郎', 'age' => 28],
        ['name' => '鈴木花子', 'age' => 32]
    ]
];

$json_file = 'data.json';
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "JSONファイルを作成しました。\n";

// JSON読み込み
$json_content = file_get_contents($json_file);
$loaded_data = json_decode($json_content, true);
echo "ユーザー数: " . count($loaded_data['users']) . "\n";

echo "\n=== ファイル情報 ===\n";

if (file_exists($filename)) {
    echo "ファイルサイズ: " . filesize($filename) . " バイト\n";
    echo "最終更新: " . date('Y-m-d H:i:s', filemtime($filename)) . "\n";
}

// クリーンアップ
unlink($filename);
unlink($csv_file);
unlink($json_file);
echo "ファイルを削除しました。\n";

?>