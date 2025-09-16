<?php

namespace PhpLearning\Testing;

use InvalidArgumentException;

/**
 * 計算機クラス - PHPUnitテスト学習用サンプル
 * 基本的な算術演算を提供するクラス
 */
class Calculator
{
    /**
     * 足し算
     */
    public function add(float $a, float $b): float
    {
        return $a + $b;
    }

    /**
     * 引き算
     */
    public function subtract(float $a, float $b): float
    {
        return $a - $b;
    }

    /**
     * 掛け算
     */
    public function multiply(float $a, float $b): float
    {
        return $a * $b;
    }

    /**
     * 割り算
     * 
     * @throws InvalidArgumentException ゼロで割ろうとした場合
     */
    public function divide(float $a, float $b): float
    {
        if ($b === 0.0) {
            throw new InvalidArgumentException('Division by zero is not allowed');
        }

        return $a / $b;
    }

    /**
     * べき乗
     */
    public function power(float $base, float $exponent): float
    {
        return pow($base, $exponent);
    }

    /**
     * 平方根
     * 
     * @throws InvalidArgumentException 負の数の平方根を求めようとした場合
     */
    public function sqrt(float $number): float
    {
        if ($number < 0) {
            throw new InvalidArgumentException('Cannot calculate square root of negative number');
        }

        return sqrt($number);
    }

    /**
     * 階乗
     * 
     * @throws InvalidArgumentException 負の数の階乗を求めようとした場合
     */
    public function factorial(int $number): int
    {
        if ($number < 0) {
            throw new InvalidArgumentException('Cannot calculate factorial of negative number');
        }

        if ($number === 0 || $number === 1) {
            return 1;
        }

        $result = 1;
        for ($i = 2; $i <= $number; $i++) {
            $result *= $i;
        }

        return $result;
    }

    /**
     * 配列の平均値
     * 
     * @param array<float> $numbers
     * @throws InvalidArgumentException 空の配列が渡された場合
     */
    public function average(array $numbers): float
    {
        if (empty($numbers)) {
            throw new InvalidArgumentException('Cannot calculate average of empty array');
        }

        return array_sum($numbers) / count($numbers);
    }

    /**
     * 配列の最大値
     * 
     * @param array<float> $numbers
     * @throws InvalidArgumentException 空の配列が渡された場合
     */
    public function max(array $numbers): float
    {
        if (empty($numbers)) {
            throw new InvalidArgumentException('Cannot find maximum of empty array');
        }

        return max($numbers);
    }

    /**
     * 配列の最小値
     * 
     * @param array<float> $numbers
     * @throws InvalidArgumentException 空の配列が渡された場合
     */
    public function min(array $numbers): float
    {
        if (empty($numbers)) {
            throw new InvalidArgumentException('Cannot find minimum of empty array');
        }

        return min($numbers);
    }

    /**
     * パーセンテージ計算
     */
    public function percentage(float $value, float $total): float
    {
        if ($total === 0.0) {
            return 0.0;
        }

        return round(($value / $total) * 100, 2);
    }

    /**
     * 複利計算
     * 
     * @param float $principal 元本
     * @param float $rate 年利率（小数）
     * @param int $time 年数
     * @param int $compoundFrequency 複利の回数（年間）
     */
    public function compoundInterest(float $principal, float $rate, int $time, int $compoundFrequency = 1): float
    {
        if ($principal < 0 || $rate < 0 || $time < 0 || $compoundFrequency <= 0) {
            throw new InvalidArgumentException('Invalid parameters for compound interest calculation');
        }

        return $principal * pow((1 + $rate / $compoundFrequency), $compoundFrequency * $time);
    }
}