<?php

namespace PhpLearning\Testing\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * ヘルパー関数のユニットテスト
 * グローバル関数のテスト例を学習できます
 */
class HelpersTest extends TestCase
{
    // ===== 通貨フォーマットのテスト =====

    /**
     * @test
     */
    public function 日本円のフォーマットができること(): void
    {
        $result = formatCurrency(1234.56, 'JPY');
        $this->assertEquals('¥1,235', $result); // 日本円は整数表示
    }

    /**
     * @test
     */
    public function 米ドルのフォーマットができること(): void
    {
        $result = formatCurrency(1234.56, 'USD');
        $this->assertEquals('$1,234.56', $result);
    }

    /**
     * @test
     */
    public function ユーロのフォーマットができること(): void
    {
        $result = formatCurrency(1234.56, 'EUR');
        $this->assertEquals('€1,234.56', $result);
    }

    /**
     * @test
     */
    public function 未知の通貨のフォーマット(): void
    {
        $result = formatCurrency(1234.56, 'GBP');
        $this->assertEquals('1,234.56 GBP', $result);
    }

    /**
     * @test
     */
    public function デフォルト通貨は日本円であること(): void
    {
        $result = formatCurrency(1000);
        $this->assertEquals('¥1,000', $result);
    }

    /**
     * @test
     * @dataProvider currencyProvider
     */
    public function 複数通貨のフォーマットテスト($amount, $currency, $expected): void
    {
        $result = formatCurrency($amount, $currency);
        $this->assertEquals($expected, $result);
    }

    public static function currencyProvider(): array
    {
        return [
            '日本円_整数' => [1000, 'JPY', '¥1,000'],
            '日本円_小数' => [1000.99, 'JPY', '¥1,001'],
            '米ドル' => [1000.50, 'USD', '$1,000.50'],
            'ユーロ' => [1000.75, 'EUR', '€1,000.75'],
            'ゼロ円' => [0, 'JPY', '¥0'],
            '負の金額' => [-500, 'USD', '$-500.00'],
        ];
    }

    // ===== スラッグ化のテスト =====

    /**
     * @test
     */
    public function 基本的なスラッグ化ができること(): void
    {
        $result = slugify('Hello World');
        $this->assertEquals('hello-world', $result);
    }

    /**
     * @test
     */
    public function 日本語のスラッグ化(): void
    {
        $result = slugify('こんにちは世界');
        // 日本語は英数字以外として'-'に変換され、複数の'-'は1つにまとめられる
        $this->assertEquals('-', $result);
    }

    /**
     * @test
     */
    public function 特殊文字を含むスラッグ化(): void
    {
        $result = slugify('Hello, World! How are you?');
        $this->assertEquals('hello-world-how-are-you', $result);
    }

    /**
     * @test
     */
    public function 数字を含むスラッグ化(): void
    {
        $result = slugify('Product 123 Review');
        $this->assertEquals('product-123-review', $result);
    }

    /**
     * @test
     */
    public function 前後のハイフンが除去されること(): void
    {
        $result = slugify('!Hello World!');
        $this->assertEquals('hello-world', $result);
    }

    /**
     * @test
     */
    public function 連続する特殊文字が1つのハイフンになること(): void
    {
        $result = slugify('Hello!!!World???');
        $this->assertEquals('hello-world', $result);
    }

    // ===== 税額計算のテスト =====

    /**
     * @test
     */
    public function デフォルト税率での計算(): void
    {
        $result = calculateTax(1000);
        $this->assertEquals(100.0, $result); // 10%
    }

    /**
     * @test
     */
    public function カスタム税率での計算(): void
    {
        $result = calculateTax(1000, 0.08);
        $this->assertEquals(80.0, $result); // 8%
    }

    /**
     * @test
     */
    public function 小数の税額計算と四捨五入(): void
    {
        $result = calculateTax(333, 0.10);
        $this->assertEquals(33.3, $result);
    }

    /**
     * @test
     */
    public function ゼロ税率での計算(): void
    {
        $result = calculateTax(1000, 0);
        $this->assertEquals(0.0, $result);
    }

    // ===== メールアドレス検証のテスト =====

    /**
     * @test
     */
    public function 有効なメールアドレスが正しく検証されること(): void
    {
        $this->assertTrue(isValidEmail('test@example.com'));
        $this->assertTrue(isValidEmail('user.name+tag@example.co.jp'));
        $this->assertTrue(isValidEmail('123@456.com'));
    }

    /**
     * @test
     */
    public function 無効なメールアドレスが正しく検証されること(): void
    {
        $this->assertFalse(isValidEmail('invalid-email'));
        $this->assertFalse(isValidEmail('@example.com'));
        $this->assertFalse(isValidEmail('test@'));
        $this->assertFalse(isValidEmail('test@.com'));
        $this->assertFalse(isValidEmail(''));
    }

    /**
     * @test
     * @dataProvider emailValidationProvider
     */
    public function メール検証のデータドリブンテスト($email, $expected): void
    {
        $result = isValidEmail($email);
        $this->assertEquals($expected, $result);
    }

    public static function emailValidationProvider(): array
    {
        return [
            ['test@example.com', true],
            ['user@domain.co.jp', true],
            ['a@b.c', true],
            ['123@456.789', true],
            ['test.email+filter@example.com', true],
            ['invalid', false],
            ['@domain.com', false],
            ['user@', false],
            ['', false],
            ['test@domain', false],
            ['test space@domain.com', false],
        ];
    }

    // ===== ランダム文字列生成のテスト =====

    /**
     * @test
     */
    public function ランダム文字列の長さが正しいこと(): void
    {
        $result = generateRandomString(10);
        $this->assertEquals(10, strlen($result));
    }

    /**
     * @test
     */
    public function デフォルト長さは10文字であること(): void
    {
        $result = generateRandomString();
        $this->assertEquals(10, strlen($result));
    }

    /**
     * @test
     */
    public function 指定した文字セットから生成されること(): void
    {
        $chars = '123';
        $result = generateRandomString(100, $chars);
        
        // 全ての文字が指定した文字セットに含まれることを確認
        for ($i = 0; $i < strlen($result); $i++) {
            $this->assertStringContainsString($result[$i], $chars);
        }
    }

    /**
     * @test
     */
    public function 複数回実行で異なる文字列が生成されること(): void
    {
        $result1 = generateRandomString(20);
        $result2 = generateRandomString(20);
        
        // 非常に稀に同じ文字列になる可能性があるが、20文字では統計的にほぼありえない
        $this->assertNotEquals($result1, $result2);
    }

    /**
     * @test
     */
    public function 英数字のみが使用されること(): void
    {
        $result = generateRandomString(100);
        
        // 英数字のパターンにマッチするかチェック
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $result);
    }

    /**
     * @test
     */
    public function ゼロ文字の文字列が生成できること(): void
    {
        $result = generateRandomString(0);
        $this->assertEquals('', $result);
    }

    /**
     * @test
     */
    public function 大量の文字列生成のパフォーマンステスト(): void
    {
        $startTime = microtime(true);
        
        // 1000個の文字列を生成
        for ($i = 0; $i < 1000; $i++) {
            generateRandomString(50);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // 1秒以内で完了することを確認
        $this->assertLessThan(1.0, $executionTime);
    }

    /**
     * @test
     */
    public function 文字の分布が均等に近いこと(): void
    {
        $result = generateRandomString(10000, '01'); // 0と1のみ
        
        $count0 = substr_count($result, '0');
        $count1 = substr_count($result, '1');
        
        // 理想は5000ずつだが、ランダムなので±20%程度の許容範囲を設ける
        $this->assertGreaterThan(4000, $count0);
        $this->assertLessThan(6000, $count0);
        $this->assertGreaterThan(4000, $count1);
        $this->assertLessThan(6000, $count1);
    }
}