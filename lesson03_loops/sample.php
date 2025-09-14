<?php

// ループ処理の基本

echo "=== for文の例 ===\n";
// for文: 指定した回数だけ繰り返し
for ($i = 1; $i <= 5; $i++) {
    echo "カウント: " . $i . "\n";
}

echo "\n=== while文の例 ===\n";
// while文: 条件が真の間繰り返し
$count = 1;
while ($count <= 3) {
    echo "while文 - カウント: " . $count . "\n";
    $count++;
}

echo "\n=== foreach文の例（配列） ===\n";
// foreach文: 配列の全要素を順次処理
$colors = ["赤", "青", "緑", "黄色"];
foreach ($colors as $color) {
    echo "色: " . $color . "\n";
}

echo "\n=== foreach文の例（連想配列） ===\n";
$student = [
    "name" => "田中太郎",
    "age" => 20,
    "subject" => "数学",
    "grade" => "A"
];

foreach ($student as $key => $value) {
    echo $key . ": " . $value . "\n";
}

echo "\n=== 九九の表（ネストしたfor文） ===\n";
for ($i = 1; $i <= 5; $i++) {
    for ($j = 1; $j <= 5; $j++) {
        $result = $i * $j;
        echo sprintf("%2d ", $result);
    }
    echo "\n";
}

echo "\n=== 条件付きループ（break, continue） ===\n";
echo "1から10までの数字で偶数のみ表示（奇数はskip）:\n";
for ($i = 1; $i <= 10; $i++) {
    if ($i % 2 !== 0) {
        continue; // 奇数の場合は次の繰り返しに進む
    }
    echo $i . " ";
}
echo "\n";

echo "\n1から10まで数えて5で停止:\n";
for ($i = 1; $i <= 10; $i++) {
    if ($i == 5) {
        echo $i . " で停止\n";
        break; // 5になったらループを抜ける
    }
    echo $i . " ";
}

echo "\n=== 配列の値を変更するループ ===\n";
$prices = [100, 200, 300, 400, 500];
echo "元の価格: ";
foreach ($prices as $price) {
    echo $price . "円 ";
}
echo "\n";

// 10%割引を適用
foreach ($prices as $key => $price) {
    $prices[$key] = $price * 0.9;
}

echo "10%割引後: ";
foreach ($prices as $price) {
    echo $price . "円 ";
}
echo "\n";

?>