<?php

// ファイル分割されたクラスの読み込み
require_once 'classes/Person.php';
require_once 'classes/Product.php';
require_once 'classes/Student.php';

// クラスとオブジェクトの基本

echo "=== 基本的なクラス使用 ===\n";

// Personクラスの使用
$person1 = new Person("田中太郎", 25, "tanaka@example.com");
$person2 = new Person("佐藤花子", 30, "sato@example.com");

echo $person1->introduce() . "\n";
echo $person2->introduce() . "\n";

// プロパティとメソッドの使用
echo $person1->name . "さんの年齢: " . $person1->age . "歳\n";
echo $person1->name . "さんのメール: " . $person1->getEmail() . "\n";

echo "\n=== 商品管理クラス ===\n";

$laptop = new Product("ノートパソコン", 89800, 5);
$mouse = new Product("マウス", 2800, 20);

// 商品情報表示
$laptopInfo = $laptop->getInfo();
echo "商品: " . $laptopInfo['name'] . "\n";
echo "価格: " . number_format($laptopInfo['price']) . "円\n";
echo "在庫: " . $laptopInfo['stock'] . "個\n";

// 販売処理
$result = $laptop->sell(2);
if ($result['success']) {
    echo $result['message'] . "\n";
    echo "売上: " . number_format($result['total']) . "円\n";
    echo "残り在庫: " . $result['remaining_stock'] . "個\n";
}

// 在庫追加
echo $laptop->addStock(3) . "\n";

echo "\n=== 継承の例 ===\n";

$student = new Student("山田太郎", 20, "yamada@student.com", "S2023001", "A");

echo $student->introduce() . "\n";
echo $student->study("数学") . "\n";

// 継承したメソッドの使用
echo $student->celebrateBirthday() . "\n";

echo "\n=== ファイル分割の利点 ===\n";
echo "- 1クラス1ファイルで管理が容易\n";
echo "- require_onceで重複読み込みを防止\n";
echo "- チーム開発での並行作業が可能\n";
echo "- クラスの再利用性が向上\n";

?>