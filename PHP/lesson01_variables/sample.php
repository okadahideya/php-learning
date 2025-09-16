<?php

// 変数の基本的な使い方

// 文字列変数
$name = "田中太郎";
$message = "こんにちは";

echo $message . "、" . $name . "さん！\n";

// 数値変数
$age = 25;
$height = 170.5;

echo $name . "さんは" . $age . "歳、身長は" . $height . "cmです。\n";

// 変数の値を変更
$age = 26;
echo "来年は" . $age . "歳になります。\n";

// 計算を含む変数
$price = 1000;
$quantity = 3;
$total = $price * $quantity;

echo "商品の単価: " . $price . "円\n";
echo "個数: " . $quantity . "個\n";
echo "合計金額: " . $total . "円\n";

// 変数の型を確認
echo "nameの型: " . gettype($name) . "\n";
echo "ageの型: " . gettype($age) . "\n";
echo "heightの型: " . gettype($height) . "\n";

?>