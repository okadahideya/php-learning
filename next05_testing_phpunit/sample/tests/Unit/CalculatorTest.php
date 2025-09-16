<?php

namespace PhpLearning\Testing\Tests\Unit;

use PhpLearning\Testing\Calculator;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * Calculatorクラスのユニットテスト
 * 基本的な算術演算のテスト例を学習できます
 */
class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    /**
     * テストの前準備
     * 各テストメソッドの前に実行される
     */
    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    /**
     * テストの後始末
     * 各テストメソッドの後に実行される
     */
    protected function tearDown(): void
    {
        // このテストでは特に必要ありませんが、
        // リソースのクリーンアップなどに使用
    }

    // ===== 足し算のテスト =====

    /**
     * @test
     * 基本的な足し算のテスト
     */
    public function 正の数の足し算ができること(): void
    {
        $result = $this->calculator->add(2, 3);
        $this->assertEquals(5, $result);
    }

    /**
     * @test
     * 負の数を含む足し算のテスト
     */
    public function 負の数を含む足し算ができること(): void
    {
        $result = $this->calculator->add(-2, 3);
        $this->assertEquals(1, $result);
    }

    /**
     * @test
     * 小数の足し算のテスト
     */
    public function 小数の足し算ができること(): void
    {
        $result = $this->calculator->add(1.5, 2.3);
        $this->assertEquals(3.8, $result, '', 0.001); // 浮動小数点の比較には誤差を考慮
    }

    // ===== 引き算のテスト =====

    /**
     * @test
     */
    public function 引き算ができること(): void
    {
        $result = $this->calculator->subtract(10, 4);
        $this->assertEquals(6, $result);
    }

    /**
     * @test
     */
    public function 負の結果になる引き算ができること(): void
    {
        $result = $this->calculator->subtract(3, 7);
        $this->assertEquals(-4, $result);
    }

    // ===== 掛け算のテスト =====

    /**
     * @test
     */
    public function 掛け算ができること(): void
    {
        $result = $this->calculator->multiply(6, 7);
        $this->assertEquals(42, $result);
    }

    /**
     * @test
     */
    public function ゼロとの掛け算ができること(): void
    {
        $result = $this->calculator->multiply(5, 0);
        $this->assertEquals(0, $result);
    }

    // ===== 割り算のテスト =====

    /**
     * @test
     */
    public function 割り算ができること(): void
    {
        $result = $this->calculator->divide(10, 2);
        $this->assertEquals(5, $result);
    }

    /**
     * @test
     */
    public function ゼロで割るとExceptionが発生すること(): void
    {
        // 例外が発生することを期待
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero is not allowed');
        
        $this->calculator->divide(10, 0);
    }

    // ===== べき乗のテスト =====

    /**
     * @test
     */
    public function べき乗計算ができること(): void
    {
        $result = $this->calculator->power(2, 3);
        $this->assertEquals(8, $result);
    }

    /**
     * @test
     */
    public function ゼロ乗は1になること(): void
    {
        $result = $this->calculator->power(5, 0);
        $this->assertEquals(1, $result);
    }

    // ===== 平方根のテスト =====

    /**
     * @test
     */
    public function 平方根計算ができること(): void
    {
        $result = $this->calculator->sqrt(16);
        $this->assertEquals(4, $result);
    }

    /**
     * @test
     */
    public function 負の数の平方根でExceptionが発生すること(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot calculate square root of negative number');
        
        $this->calculator->sqrt(-4);
    }

    // ===== 階乗のテスト =====

    /**
     * @test
     */
    public function 階乗計算ができること(): void
    {
        $result = $this->calculator->factorial(5);
        $this->assertEquals(120, $result); // 5! = 5 × 4 × 3 × 2 × 1 = 120
    }

    /**
     * @test
     */
    public function ゼロの階乗は1であること(): void
    {
        $result = $this->calculator->factorial(0);
        $this->assertEquals(1, $result);
    }

    /**
     * @test
     */
    public function 負の数の階乗でExceptionが発生すること(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot calculate factorial of negative number');
        
        $this->calculator->factorial(-1);
    }

    // ===== 平均値のテスト =====

    /**
     * @test
     */
    public function 配列の平均値が計算できること(): void
    {
        $numbers = [1, 2, 3, 4, 5];
        $result = $this->calculator->average($numbers);
        $this->assertEquals(3, $result);
    }

    /**
     * @test
     */
    public function 空の配列でExceptionが発生すること(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot calculate average of empty array');
        
        $this->calculator->average([]);
    }

    // ===== 最大値・最小値のテスト =====

    /**
     * @test
     */
    public function 配列の最大値が取得できること(): void
    {
        $numbers = [3, 1, 4, 1, 5, 9, 2, 6];
        $result = $this->calculator->max($numbers);
        $this->assertEquals(9, $result);
    }

    /**
     * @test
     */
    public function 配列の最小値が取得できること(): void
    {
        $numbers = [3, 1, 4, 1, 5, 9, 2, 6];
        $result = $this->calculator->min($numbers);
        $this->assertEquals(1, $result);
    }

    // ===== パーセンテージ計算のテスト =====

    /**
     * @test
     */
    public function パーセンテージ計算ができること(): void
    {
        $result = $this->calculator->percentage(25, 100);
        $this->assertEquals(25.0, $result);
    }

    /**
     * @test
     */
    public function 総計がゼロの場合のパーセンテージ計算(): void
    {
        $result = $this->calculator->percentage(10, 0);
        $this->assertEquals(0.0, $result);
    }

    // ===== 複利計算のテスト =====

    /**
     * @test
     */
    public function 複利計算ができること(): void
    {
        // 元本100万円、年利5%、10年間、年1回複利
        $result = $this->calculator->compoundInterest(1000000, 0.05, 10, 1);
        
        // 期待値: 1000000 * (1.05)^10 ≈ 1628895
        $this->assertEqualsWithDelta(1628894.63, $result, 0.01);
    }

    /**
     * @test
     */
    public function 不正なパラメータで複利計算のExceptionが発生すること(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parameters for compound interest calculation');
        
        $this->calculator->compoundInterest(-1000, 0.05, 10, 1);
    }

    // ===== データプロバイダーを使用したテスト =====

    /**
     * @test
     * @dataProvider additionProvider
     */
    public function データプロバイダーを使った足し算テスト($a, $b, $expected): void
    {
        $result = $this->calculator->add($a, $b);
        $this->assertEquals($expected, $result);
    }

    /**
     * 足し算のテストデータ
     */
    public static function additionProvider(): array
    {
        return [
            '正数同士' => [1, 2, 3],
            '負数を含む' => [-1, 2, 1],
            'ゼロを含む' => [0, 5, 5],
            '小数' => [1.5, 2.5, 4.0],
            '大きな数' => [999999, 1, 1000000],
        ];
    }

    // ===== パフォーマンステスト =====

    /**
     * @test
     * @group performance
     */
    public function 大量計算のパフォーマンステスト(): void
    {
        $startTime = microtime(true);
        
        // 10000回の計算を実行
        for ($i = 0; $i < 10000; $i++) {
            $this->calculator->add($i, $i + 1);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // 1秒以内で完了することを確認
        $this->assertLessThan(1.0, $executionTime, '10000回の計算は1秒以内に完了すべき');
    }

    // ===== 境界値テスト =====

    /**
     * @test
     */
    public function 非常に大きな数の計算(): void
    {
        $result = $this->calculator->add(PHP_FLOAT_MAX / 2, PHP_FLOAT_MAX / 2);
        $this->assertIsFloat($result);
        $this->assertFinite($result);
    }

    /**
     * @test
     */
    public function 非常に小さな数の計算(): void
    {
        $result = $this->calculator->add(PHP_FLOAT_MIN, PHP_FLOAT_MIN);
        $this->assertGreaterThan(0, $result);
    }
}