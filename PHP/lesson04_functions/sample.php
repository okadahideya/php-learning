<?php

// 関数の基本的な使い方

// 基本的な関数の定義と呼び出し
function sayHello() {
    echo "こんにちは！\n";
}

echo "=== 基本的な関数 ===\n";
sayHello();

// 引数を受け取る関数
function greet($name) {
    echo "こんにちは、" . $name . "さん！\n";
}

echo "\n=== 引数を持つ関数 ===\n";
greet("田中");
greet("佐藤");

// 複数の引数を受け取る関数
function introduce($name, $age, $hobby) {
    echo $name . "さんは" . $age . "歳で、趣味は" . $hobby . "です。\n";
}

echo "\n=== 複数の引数を持つ関数 ===\n";
introduce("山田太郎", 25, "読書");
introduce("鈴木花子", 30, "料理");

// 戻り値を返す関数
function add($a, $b) {
    return $a + $b;
}

function multiply($a, $b) {
    return $a * $b;
}

echo "\n=== 戻り値を返す関数 ===\n";
$sum = add(5, 3);
echo "5 + 3 = " . $sum . "\n";

$product = multiply(4, 6);
echo "4 × 6 = " . $product . "\n";

// デフォルト引数を持つ関数
function createMessage($message, $prefix = "メッセージ") {
    return $prefix . ": " . $message;
}

echo "\n=== デフォルト引数を持つ関数 ===\n";
echo createMessage("今日は良い天気です") . "\n";
echo createMessage("明日は雨です", "天気予報") . "\n";

// 配列を扱う関数
function getArraySum($numbers) {
    $total = 0;
    foreach ($numbers as $number) {
        $total += $number;
    }
    return $total;
}

function getArrayInfo($array) {
    return [
        "count" => count($array),
        "max" => max($array),
        "min" => min($array),
        "average" => array_sum($array) / count($array)
    ];
}

echo "\n=== 配列を扱う関数 ===\n";
$scores = [85, 92, 78, 96, 88];
echo "点数: " . implode(", ", $scores) . "\n";
echo "合計: " . getArraySum($scores) . "点\n";

$info = getArrayInfo($scores);
echo "件数: " . $info["count"] . "\n";
echo "最高点: " . $info["max"] . "点\n";
echo "最低点: " . $info["min"] . "点\n";
echo "平均点: " . round($info["average"], 1) . "点\n";

// スコープの例（グローバル変数とローカル変数）
$globalVar = "グローバル変数";

function showScope() {
    $localVar = "ローカル変数";
    global $globalVar;
    
    echo "関数内から: " . $globalVar . "\n";
    echo "関数内から: " . $localVar . "\n";
}

echo "\n=== 変数のスコープ ===\n";
echo "関数外から: " . $globalVar . "\n";
showScope();

// 再帰関数の例（階乗計算）
function factorial($n) {
    if ($n <= 1) {
        return 1;
    }
    return $n * factorial($n - 1);
}

echo "\n=== 再帰関数（階乗） ===\n";
echo "5! = " . factorial(5) . "\n";
echo "3! = " . factorial(3) . "\n";

?>