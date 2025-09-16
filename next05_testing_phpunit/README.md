# Next Step 05: テスト駆動開発（TDD）

## 学習目標
- PHPUnitを使ったテストの基本を習得する
- テスト駆動開発（TDD）の実践方法を理解する
- 品質の高いコードを書くテクニックを身につける
- CIでの自動テスト実行を学ぶ

## 前提条件
- PHP基礎学習完了
- オブジェクト指向プログラミングの理解
- Composerの基本操作

## テストの重要性

### なぜテストが必要？
1. **バグの早期発見**: 開発段階でエラーを検出
2. **リファクタリングの安全性**: 変更時の影響確認
3. **仕様の明確化**: テストが仕様書の役割
4. **チーム開発の効率化**: 品質の担保

### テストの種類
- **単体テスト（Unit Test）**: 個別のクラス・メソッド
- **統合テスト（Integration Test）**: 複数モジュールの連携
- **機能テスト（Feature Test）**: エンドユーザー視点の機能

## PHPUnit セットアップ

### インストール
```bash
# Composerでインストール
composer require --dev phpunit/phpunit

# グローバルインストール（オプション）
composer global require phpunit/phpunit
```

### 設定ファイル（phpunit.xml）
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.0/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./src</directory>
        </include>
    </source>
</phpunit>
```

### ディレクトリ構造
```
project/
├── src/                 # アプリケーションコード
│   ├── Calculator.php
│   ├── User.php
│   └── UserService.php
├── tests/               # テストコード
│   ├── Unit/           # 単体テスト
│   │   ├── CalculatorTest.php
│   │   └── UserTest.php
│   └── Feature/        # 機能テスト
│       └── UserServiceTest.php
├── vendor/
├── composer.json
└── phpunit.xml
```

## 基本的なテスト

### 最初のテスト
```php
<?php
// src/Calculator.php
class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
    
    public function divide($a, $b) {
        if ($b === 0) {
            throw new InvalidArgumentException('Division by zero');
        }
        return $a / $b;
    }
}
```

```php
<?php
// tests/Unit/CalculatorTest.php
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase {
    private Calculator $calculator;
    
    protected function setUp(): void {
        $this->calculator = new Calculator();
    }
    
    public function testAdd() {
        $result = $this->calculator->add(2, 3);
        $this->assertEquals(5, $result);
    }
    
    public function testAddWithNegativeNumbers() {
        $result = $this->calculator->add(-2, 3);
        $this->assertEquals(1, $result);
    }
    
    public function testDivide() {
        $result = $this->calculator->divide(10, 2);
        $this->assertEquals(5, $result);
    }
    
    public function testDivideByZeroThrowsException() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero');
        
        $this->calculator->divide(10, 0);
    }
}
```

### テスト実行
```bash
# 全テスト実行
./vendor/bin/phpunit

# 特定のテストクラス実行
./vendor/bin/phpunit tests/Unit/CalculatorTest.php

# 特定のテストメソッド実行
./vendor/bin/phpunit --filter testAdd

# カバレッジレポート生成
./vendor/bin/phpunit --coverage-html coverage/
```

## アサーション（検証）

### 基本的なアサーション
```php
<?php
class AssertionExampleTest extends TestCase {
    public function testBasicAssertions() {
        // 等価性
        $this->assertEquals(5, 2 + 3);
        $this->assertNotEquals(4, 2 + 3);
        
        // 同一性
        $user1 = new User('John');
        $user2 = $user1;
        $this->assertSame($user1, $user2);
        
        // 真偽値
        $this->assertTrue(is_string('hello'));
        $this->assertFalse(is_numeric('hello'));
        
        // null チェック
        $this->assertNull(null);
        $this->assertNotNull('something');
        
        // 配列
        $this->assertContains('apple', ['apple', 'banana']);
        $this->assertCount(3, [1, 2, 3]);
        
        // 文字列
        $this->assertStringContains('world', 'hello world');
        $this->assertStringStartsWith('hello', 'hello world');
        
        // 型チェック
        $this->assertIsArray([1, 2, 3]);
        $this->assertIsString('hello');
        $this->assertIsInt(123);
    }
}
```

## TDD実践例

### ユーザー管理システムのTDD
```php
<?php
// 1. テストを最初に書く（Red）
class UserServiceTest extends TestCase {
    private UserService $userService;
    
    protected function setUp(): void {
        $this->userService = new UserService();
    }
    
    public function testCreateUserWithValidData() {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secure123'
        ];
        
        $user = $this->userService->createUser($userData);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john@example.com', $user->getEmail());
        $this->assertNotEquals('secure123', $user->getPassword()); // ハッシュ化確認
    }
    
    public function testCreateUserWithInvalidEmailThrowsException() {
        $userData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'secure123'
        ];
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $this->userService->createUser($userData);
    }
}
```

```php
<?php
// 2. 最小限の実装（Green）
class UserService {
    public function createUser(array $userData): User {
        $this->validateUserData($userData);
        
        return new User(
            $userData['name'],
            $userData['email'],
            password_hash($userData['password'], PASSWORD_DEFAULT)
        );
    }
    
    private function validateUserData(array $userData): void {
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        
        if (strlen($userData['password']) < 6) {
            throw new InvalidArgumentException('Password too short');
        }
    }
}
```

## モック・スタブ

### データベース操作のテスト
```php
<?php
class UserRepositoryTest extends TestCase {
    public function testSaveUser() {
        // モック作成
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);
        
        // モックの動作設定
        $pdoMock->expects($this->once())
                ->method('prepare')
                ->with('INSERT INTO users (name, email) VALUES (?, ?)')
                ->willReturn($stmtMock);
                
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->with(['John Doe', 'john@example.com'])
                 ->willReturn(true);
        
        // テスト実行
        $repository = new UserRepository($pdoMock);
        $user = new User('John Doe', 'john@example.com');
        
        $result = $repository->save($user);
        $this->assertTrue($result);
    }
}
```

### 外部API呼び出しのテスト
```php
<?php
class EmailServiceTest extends TestCase {
    public function testSendEmailSuccess() {
        // HTTPクライアントのモック
        $httpClientMock = $this->createMock(HttpClient::class);
        
        $httpClientMock->expects($this->once())
                       ->method('post')
                       ->with('/send-email', [
                           'to' => 'user@example.com',
                           'subject' => 'Welcome',
                           'body' => 'Welcome to our service!'
                       ])
                       ->willReturn(['status' => 'success']);
        
        $emailService = new EmailService($httpClientMock);
        $result = $emailService->sendWelcomeEmail('user@example.com');
        
        $this->assertTrue($result);
    }
}
```

## データプロバイダー

### 複数のデータでテスト
```php
<?php
class ValidationTest extends TestCase {
    /**
     * @dataProvider emailProvider
     */
    public function testEmailValidation($email, $expected) {
        $validator = new EmailValidator();
        $result = $validator->isValid($email);
        
        $this->assertEquals($expected, $result);
    }
    
    public function emailProvider(): array {
        return [
            ['valid@example.com', true],
            ['user.name@domain.co.jp', true],
            ['invalid-email', false],
            ['@example.com', false],
            ['user@', false],
            ['', false],
        ];
    }
    
    /**
     * @dataProvider passwordProvider
     */
    public function testPasswordStrength($password, $expectedStrength) {
        $validator = new PasswordValidator();
        $strength = $validator->getStrength($password);
        
        $this->assertEquals($expectedStrength, $strength);
    }
    
    public function passwordProvider(): array {
        return [
            ['123', 'weak'],
            ['password', 'weak'],
            ['Password123', 'medium'],
            ['Password123!@#', 'strong'],
        ];
    }
}
```

## 実装課題

### 課題1: 基本的なテスト作成
1. ユーザークラスの単体テスト
2. 計算機能のテスト（境界値含む）
3. バリデーション機能のテスト

### 課題2: TDDでの機能開発
1. ショッピングカート機能をTDDで実装
2. 商品在庫管理システム
3. 注文処理システム

### 課題3: 統合テスト
1. API エンドポイントのテスト
2. データベース操作を含むテスト
3. 外部サービス連携のテスト

## CI/CD でのテスト自動化

### GitHub Actions
```yaml
# .github/workflows/tests.yml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: test_db
        ports:
          - 3306:3306
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql
        coverage: xdebug
    
    - name: Cache Composer packages
      id: composer-cache
      uses: actions/cache@v3
      with:
        path: vendor
        key: ${{ runner.os }}-php-${{ hashFiles('**/composer.lock') }}
        restore-keys: |
          ${{ runner.os }}-php-
    
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
    
    - name: Run tests
      run: ./vendor/bin/phpunit --coverage-clover coverage.xml
    
    - name: Upload coverage to Codecov
      uses: codecov/codecov-action@v3
      with:
        file: ./coverage.xml
```

## テスト品質向上

### コードカバレッジ
```bash
# HTML形式でカバレッジレポート生成
./vendor/bin/phpunit --coverage-html coverage/

# カバレッジ率確認
./vendor/bin/phpunit --coverage-text
```

### 静的解析ツール
```bash
# PHPStan
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse src

# Psalm
composer require --dev vimeo/psalm
./vendor/bin/psalm --init
./vendor/bin/psalm
```

### ベストプラクティス
1. **テスト名は説明的に**: `testCalculatesTaxCorrectly`
2. **AAA パターン**: Arrange, Act, Assert の順序
3. **1テスト1検証**: 一つのテストで一つの事を検証
4. **テストは独立性を保つ**: 他のテストに依存しない
5. **エッジケースもテスト**: 境界値、異常系も含める

## 次のステップ
これで実践的なPHP開発に必要な技術スタックを一通り学習完了です！

### さらなる学習
1. **Laravel上級**: Eloquent、Middleware、Events
2. **API開発**: RESTful API、GraphQL
3. **マイクロサービス**: 分散システム設計
4. **DevOps**: Kubernetes、監視・ロギング
5. **セキュリティ**: 脆弱性対策、認証・認可