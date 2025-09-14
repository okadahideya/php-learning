<?php

// 配列の基本的な使い方

// インデックス配列（数値インデックス）
$fruits = ["りんご", "バナナ", "オレンジ", "ぶどう"];

echo "果物の配列:\n";
echo $fruits[0] . "\n";  // りんご
echo $fruits[1] . "\n";  // バナナ
echo $fruits[2] . "\n";  // オレンジ
echo $fruits[3] . "\n";  // ぶどう

// 配列の要素数を取得
echo "果物の種類数: " . count($fruits) . "\n";

// 配列に要素を追加
$fruits[] = "いちご";
echo "いちごを追加しました。\n";
echo "果物の種類数: " . count($fruits) . "\n";

echo "\n";

// 連想配列（キーと値のペア）
$student = [
    "name" => "山田花子",
    "age" => 20,
    "grade" => "A",
    "subject" => "数学"
];

echo "学生情報:\n";
echo "名前: " . $student["name"] . "\n";
echo "年齢: " . $student["age"] . "歳\n";
echo "成績: " . $student["grade"] . "\n";
echo "科目: " . $student["subject"] . "\n";

echo "\n";

// 配列の全要素を表示（foreach文）
echo "全ての果物:\n";
foreach ($fruits as $fruit) {
    echo "- " . $fruit . "\n";
}

echo "\n学生情報の詳細:\n";
foreach ($student as $key => $value) {
    echo $key . ": " . $value . "\n";
}

// 多次元配列
$students = [
    ["name" => "田中太郎", "age" => 22, "subject" => "物理"],
    ["name" => "佐藤次郎", "age" => 21, "subject" => "化学"],
    ["name" => "鈴木三郎", "age" => 23, "subject" => "生物"]
];

echo "\n全学生情報:\n";
foreach ($students as $index => $student) {
    echo ($index + 1) . ". " . $student["name"] . " (" . $student["age"] . "歳) - " . $student["subject"] . "\n";
}

?>