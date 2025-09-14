# Lesson 05: クラスとオブジェクト指向（ファイル分割）

## 学習目標
- クラスとオブジェクトの概念を理解する
- プロパティとメソッドを定義できるようになる
- アクセス修飾子（public、private、protected）を理解する
- コンストラクタとgetterメソッドを作成できるようになる
- 継承の基本を理解する
- ファイル分割とrequire_onceの使い方を覚える

## オブジェクト指向とは
オブジェクト指向プログラミング（OOP）は、関連するデータ（プロパティ）と処理（メソッド）をひとつのクラスにまとめる手法です。

## 基本概念

### 1. クラス（設計図）
```php
class ClassName {
    // プロパティ（データ）
    public $property1;
    private $property2;
    
    // コンストラクタ
    public function __construct($param1, $param2) {
        $this->property1 = $param1;
        $this->property2 = $param2;
    }
    
    // メソッド（処理）
    public function methodName() {
        return $this->property1;
    }
}
```

### 2. オブジェクト（インスタンス）
```php
$object = new ClassName("value1", "value2");
$result = $object->methodName();
```

## アクセス修飾子
- **public**: どこからでもアクセス可能
- **private**: 同じクラス内からのみアクセス可能
- **protected**: 同じクラスと継承先からアクセス可能

## 重要なメソッド
- **コンストラクタ**: `__construct()` - オブジェクト作成時に自動実行
- **getterメソッド**: プロパティの値を取得するメソッド
- **setterメソッド**: プロパティの値を設定するメソッド

## 課題内容

### 1. sample.php を実行してみよう
まず見本コードを実行して、クラスとオブジェクトがどのように動作するかを確認してください。

```bash
php sample.php
```

### 2. practice.php を完成させよう
practice.php に書かれているコメントの指示に従って、クラスを実装してください。

#### 実装すべき内容

**1. Carクラス（classes/Car.phpに作成）**
- プロパティ: `brand`, `model`, `year`, `mileage`（全てprivate）
- メソッド: コンストラクタ、各getter、`getCarInfo()`, `drive($distance)`

**2. BankAccountクラス（classes/BankAccount.phpに作成）**
- プロパティ: `accountNumber`（protected）, `balance`（private）, `ownerName`（protected）
- メソッド: コンストラクタ、各getter、`deposit($amount)`, `withdraw($amount)`, `checkBalance()`

### 3. 完成したら実行してみよう
```bash
php practice.php
```

## ポイント
- `$this->` を使って自分のプロパティやメソッドにアクセス
- privateプロパティはgetterメソッド経由でアクセス
- コンストラクタで初期値を設定
- メソッド名は動詞で始める（get, set, check, calculateなど）
- **ファイル分割**: 1クラス1ファイルで管理する
- **require_once**: ファイルの重複読み込みを防ぐ

## オブジェクト指向の利点
1. **カプセル化**: 関連するデータと処理をまとめる
2. **再利用性**: 一度作ったクラスは何度でも使える
3. **保守性**: コードの変更が局所化される
4. **拡張性**: 継承により機能を拡張できる

## 実用例
- ユーザー管理（Userクラス）
- 商品管理（Productクラス）
- データベース接続（Databaseクラス）
- ファイル操作（FileHandlerクラス）

## 次のステップ
基本的なクラスが作れるようになったら、以下を学習しましょう：
- インターフェース
- 抽象クラス
- トレイト
- 名前空間