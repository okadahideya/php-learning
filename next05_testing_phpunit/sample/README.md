# PHPUnitテスト基礎学習サンプル

PHP学習者向けのPHPUnitを使ったテスト駆動開発（TDD）を学習するためのサンプルプロジェクトです。

## 📋 概要

このサンプルでは、以下のテスト技術を学習できます：

- **PHPUnit基礎**: テストの書き方・実行方法・アサーション
- **ユニットテスト**: 関数・クラスメソッドの単体テスト
- **機能テスト**: 複数コンポーネントの統合テスト
- **モック**: 依存関係の分離・テストダブル
- **テストケース設計**: 境界値・例外・エッジケース
- **コードカバレッジ**: テスト網羅率の測定

## 🚀 セットアップ方法

### 1. 前提条件
```bash
# PHP 8.0以上
php --version

# Composerが必要
composer --version
```

### 2. 依存関係のインストール
```bash
cd next05_testing_phpunit/sample
composer install
```

### 3. テストの実行
```bash
# 全テスト実行
composer test

# または直接実行
vendor/bin/phpunit
```

## 📁 プロジェクト構成

```
sample/
├── src/                      # アプリケーションコード
│   ├── Calculator.php        # 計算機クラス（算術演算）
│   ├── User.php             # ユーザークラス（ドメインモデル）
│   ├── UserRepository.php    # ユーザーリポジトリ（データアクセス）
│   ├── DatabaseInterface.php # データベースインターフェース
│   └── helpers.php          # ヘルパー関数
├── tests/                   # テストコード
│   ├── Unit/               # ユニットテスト
│   │   ├── CalculatorTest.php
│   │   ├── UserTest.php
│   │   └── HelpersTest.php
│   └── Feature/            # 機能テスト
│       └── UserRepositoryTest.php
├── composer.json            # Composer設定
├── phpunit.xml             # PHPUnit設定
└── README.md               # このファイル
```

## 🔍 学習内容

### 1. PHPUnit基礎

#### 基本的なテストクラス
```php
<?php
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * @test
     */
    public function 足し算ができること(): void
    {
        $calculator = new Calculator();
        $result = $calculator->add(2, 3);
        
        $this->assertEquals(5, $result);
    }
}
```

#### セットアップとティアダウン
```php
protected function setUp(): void
{
    // 各テストメソッドの前に実行
    $this->calculator = new Calculator();
}

protected function tearDown(): void
{
    // 各テストメソッドの後に実行
    // リソースのクリーンアップなど
}
```

### 2. アサーション（検証）

#### 基本的なアサーション
```php
// 等価性の検証
$this->assertEquals(期待値, 実際の値);
$this->assertSame(期待値, 実際の値); // より厳密（型も含む）

// 真偽値の検証
$this->assertTrue($user->isActive());
$this->assertFalse($user->isDeleted());

// null の検証
$this->assertNull($result);
$this->assertNotNull($result);

// 型の検証
$this->assertIsArray($data);
$this->assertIsString($name);
$this->assertInstanceOf(User::class, $user);

// 文字列の検証
$this->assertStringContainsString('error', $message);
$this->assertMatchesRegularExpression('/^\d+$/', $id);

// 配列の検証
$this->assertContains('admin', $user->getRoles());
$this->assertCount(3, $items);
$this->assertEmpty($errors);

// 数値の検証
$this->assertGreaterThan(0, $age);
$this->assertLessThan(100, $score);
$this->assertEqualsWithDelta(3.14, $pi, 0.01); // 誤差を考慮
```

### 3. 例外テスト
```php
/**
 * @test
 */
public function ゼロで割るとExceptionが発生すること(): void
{
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Division by zero is not allowed');
    
    $this->calculator->divide(10, 0);
}
```

### 4. データプロバイダー
```php
/**
 * @test
 * @dataProvider additionProvider
 */
public function データプロバイダーを使った足し算テスト($a, $b, $expected): void
{
    $result = $this->calculator->add($a, $b);
    $this->assertEquals($expected, $result);
}

public static function additionProvider(): array
{
    return [
        '正数同士' => [1, 2, 3],
        '負数を含む' => [-1, 2, 1],
        'ゼロを含む' => [0, 5, 5],
        '小数' => [1.5, 2.5, 4.0],
    ];
}
```

### 5. モックオブジェクト
```php
protected function setUp(): void
{
    // インターフェースのモックを作成
    $this->databaseMock = $this->createMock(DatabaseInterface::class);
    $this->repository = new UserRepository($this->databaseMock);
}

/**
 * @test
 */
public function ユーザーが正しく保存されること(): void
{
    // モックの振る舞いを定義
    $this->databaseMock
        ->expects($this->once())           // 1回呼ばれることを期待
        ->method('insert')                 // insertメソッドが
        ->with('users', $expectedData)     // 指定の引数で
        ->willReturn(true);                // trueを返す

    // テスト対象のメソッドを実行
    $this->repository->save($user);
}
```

### 6. テストグループ
```php
/**
 * @test
 * @group performance
 */
public function 大量計算のパフォーマンステスト(): void
{
    // パフォーマンステスト
}
```

```bash
# 特定のグループのみ実行
vendor/bin/phpunit --group performance
```

## 🎯 テストケース設計

### 1. 等価分割法
入力値を同等なグループに分割してテストケースを作成
```php
// 年齢による分類
public static function ageTestProvider(): array
{
    return [
        '子供' => [5, false],      // 0-17歳
        '成人' => [25, true],      // 18-64歳  
        '高齢者' => [70, true],    // 65歳以上
    ];
}
```

### 2. 境界値分析
境界付近の値でテストケースを作成
```php
/**
 * @test
 * @dataProvider boundaryValueProvider
 */
public function 年齢境界値テスト($age, $expected): void
{
    $user = new User('Test', 'test@example.com', new DateTime("-{$age} years"));
    $this->assertEquals($expected, $user->isAdult());
}

public static function boundaryValueProvider(): array
{
    return [
        '17歳' => [17, false],  // 境界値-1
        '18歳' => [18, true],   // 境界値
        '19歳' => [19, true],   // 境界値+1
    ];
}
```

### 3. エラー条件テスト
```php
/**
 * @test
 */
public function 無効な入力でExceptionが発生すること(): void
{
    $this->expectException(InvalidArgumentException::class);
    $this->user->setEmail('invalid-email');
}
```

## 📊 コードカバレッジ

### カバレッジレポートの生成
```bash
# HTMLレポート生成
composer test-coverage

# テキストレポート
composer test-coverage-text

# XMLレポート（CI用）
vendor/bin/phpunit --coverage-xml coverage
```

### カバレッジの種類
- **行カバレッジ**: 実行された行の割合
- **関数カバレッジ**: 実行された関数の割合
- **ブランチカバレッジ**: 実行された分岐の割合

### カバレッジの目安
- **80%以上**: 良好
- **90%以上**: 優秀
- **100%**: 理想的だが、現実的には不要な場合も多い

## 🔧 コマンド集

```bash
# 基本コマンド
composer test              # 全テスト実行
composer test-unit         # ユニットテストのみ
composer test-feature      # 機能テストのみ

# カバレッジ
composer test-coverage     # HTMLカバレッジレポート
composer test-coverage-text # テキストカバレッジレポート

# 静的解析
composer analyse          # PHPStan実行
composer cs-check        # コーディング規約チェック
composer cs-fix          # コーディング規約自動修正
composer md              # PHP Mess Detector

# 品質チェック一括実行
composer quality         # 全品質チェックツール実行
```

### PHPUnitオプション
```bash
# 特定のテストファイルのみ実行
vendor/bin/phpunit tests/Unit/CalculatorTest.php

# 特定のテストメソッドのみ実行
vendor/bin/phpunit --filter testAddition

# テストグループを指定
vendor/bin/phpunit --group performance

# 失敗時に詳細表示
vendor/bin/phpunit --verbose

# 失敗時に停止
vendor/bin/phpunit --stop-on-failure

# テスト実行時間を表示
vendor/bin/phpunit --verbose --debug
```

## 🏗️ テスト駆動開発（TDD）

### TDDのサイクル
1. **Red**: 失敗するテストを書く
2. **Green**: テストを通す最小限のコードを書く  
3. **Refactor**: コードを改善する

### TDDの実践例
```php
// 1. Red: まず失敗するテストを書く
/**
 * @test
 */
public function ユーザー名をフルネームで取得できること(): void
{
    $user = new User('田中', '太郎', 'tanaka@example.com');
    $this->assertEquals('田中 太郎', $user->getFullName());
}

// 2. Green: テストを通す最小限のコード
class User 
{
    private string $firstName;
    private string $lastName;
    
    public function __construct(string $lastName, string $firstName, string $email)
    {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
    }
    
    public function getFullName(): string
    {
        return $this->lastName . ' ' . $this->firstName;
    }
}

// 3. Refactor: コードを改善（この例では特に改善点なし）
```

## 🔍 デバッグ技法

### テストデバッグ
```php
/**
 * @test
 */
public function デバッグ例(): void
{
    $result = $this->calculator->add(2, 3);
    
    // デバッグ出力
    var_dump($result);
    echo "Result: {$result}\n";
    
    $this->assertEquals(5, $result);
}
```

### テスト失敗時の詳細情報
```php
// カスタムメッセージ付きアサーション
$this->assertEquals($expected, $actual, "計算結果が期待値と異なります");

// 複雑なオブジェクトの比較
$this->assertEquals($expectedUser->toArray(), $actualUser->toArray());
```

## ⚡ パフォーマンステスト

```php
/**
 * @test
 * @group performance
 */
public function 大量データ処理のパフォーマンス(): void
{
    $startTime = microtime(true);
    
    // 大量データの処理
    for ($i = 0; $i < 10000; $i++) {
        $this->calculator->add($i, $i + 1);
    }
    
    $endTime = microtime(true);
    $executionTime = $endTime - $startTime;
    
    // 1秒以内で完了することを確認
    $this->assertLessThan(1.0, $executionTime);
}
```

## 🎯 ベストプラクティス

### 1. テスト名の付け方
```php
// ❌ 悪い例
public function testAdd(): void

// ✅ 良い例  
public function 正の数の足し算ができること(): void
public function ゼロで割るとExceptionが発生すること(): void
```

### 2. テストの構造（AAA）
```php
/**
 * @test
 */
public function ユーザーが作成できること(): void
{
    // Arrange（準備）
    $name = '田中太郎';
    $email = 'tanaka@example.com';
    $birthDate = new DateTime('1990-01-01');
    
    // Act（実行）
    $user = new User($name, $email, $birthDate);
    
    // Assert（検証）
    $this->assertEquals($name, $user->getName());
    $this->assertEquals($email, $user->getEmail());
}
```

### 3. テストの独立性
```php
// ❌ 悪い例：テスト間で状態を共有
class UserTest extends TestCase 
{
    private static $user; // 静的プロパティで状態共有
    
    public function testCreate(): void
    {
        self::$user = new User('Test', 'test@example.com');
        // ...
    }
    
    public function testUpdate(): void
    {
        self::$user->setName('Updated'); // 前のテストに依存
        // ...
    }
}

// ✅ 良い例：各テストで独立してセットアップ
class UserTest extends TestCase 
{
    protected function setUp(): void
    {
        $this->user = new User('Test', 'test@example.com', new DateTime('1990-01-01'));
    }
}
```

### 4. 適切なアサーション
```php
// ❌ 曖昧なアサーション
$this->assertTrue($user->getAge() > 0);

// ✅ 具体的なアサーション
$this->assertEquals(34, $user->getAge());
```

## 🚀 発展学習

### 1. より高度なテスト技法
- **スタブ**: メソッドの戻り値を固定
- **スパイ**: メソッド呼び出しを記録・検証
- **フェイク**: 軽量な実装を提供

### 2. 統合テスト
```php
/**
 * @test
 * @group integration
 */
public function データベース連携テスト(): void
{
    // 実際のデータベースと連携するテスト
    $pdo = new PDO('sqlite::memory:');
    $repository = new UserRepository(new SqliteDatabase($pdo));
    
    $user = new User('Test', 'test@example.com', new DateTime('1990-01-01'));
    $repository->save($user);
    
    $foundUser = $repository->findByEmail('test@example.com');
    $this->assertEquals('Test', $foundUser->getName());
}
```

### 3. E2Eテスト（エンドツーエンドテスト）
- **Selenium**: ブラウザ自動化
- **Behat**: BDD（振る舞い駆動開発）フレームワーク

### 4. CI/CD統合
```yaml
# .github/workflows/ci.yml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          coverage: xdebug
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: composer test-coverage-text
```

## 📚 参考資料

- [PHPUnit公式ドキュメント](https://phpunit.de/)
- [Mockery公式ドキュメント](http://docs.mockery.io/)
- [PHPStan公式サイト](https://phpstan.org/)
- [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- [テスト駆動開発](https://www.ohmsha.co.jp/book/9784274217883/) - Kent Beck著

---

**このサンプルを通じて、品質の高いPHPコードを書くためのテスト技法を身につけましょう！**