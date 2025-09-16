<?php

// ファイル分割構成の実練：クラスを別ファイルに作成して読み込んでください

// 1. classes/Car.php ファイルを作成し、Carクラスを定義してください
//    プロパティ: brand（メーカー）, model（車種）, year（年式）, mileage（走行距離）
//    全てprivateプロパティにしてください

// 作成したCarクラスを読み込んでください
// require_once 'classes/Car.php';

// 2. classes/BankAccount.php ファイルを作成し、BankAccountクラスを定義してください
//    プロパティ: accountNumber（口座番号）, balance（残高）, ownerName（口座名義）
//    balanceはprivate、その他はprotectedにしてください

// 作成したBankAccountクラスを読み込んでください
// require_once 'classes/BankAccount.php';

// 作成したクラスをテストするコード
echo "=== クラスのテスト ===\n\n";

// Carクラスのテスト
echo "=== Carクラスのテスト ===\n";
$myCar = new Car("Toyota", "Prius", 2020, 15000);

// ここで作成したメソッドをテストしてください
// 例：
// echo $myCar->getCarInfo() . "\n";
// echo $myCar->drive(100) . "\n";

echo "\n=== BankAccountクラスのテスト ===\n";
$myAccount = new BankAccount("123-456-789", 50000, "田中太郎");

// ここで作成したメソッドをテストしてください
// 例：
// echo $myAccount->checkBalance() . "\n";
// echo $myAccount->deposit(10000) . "\n";
// echo $myAccount->withdraw(5000) . "\n";
// echo $myAccount->withdraw(60000) . "\n"; // 残高不足のテスト

?>