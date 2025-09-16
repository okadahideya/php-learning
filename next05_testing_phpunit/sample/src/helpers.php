<?php
/**
 * PHPUnitテスト学習サンプル - ヘルパー関数
 */

if (!function_exists('formatCurrency')) {
    /**
     * 通貨フォーマット
     */
    function formatCurrency(float $amount, string $currency = 'JPY'): string
    {
        switch ($currency) {
            case 'JPY':
                return '¥' . number_format($amount);
            case 'USD':
                return '$' . number_format($amount, 2);
            case 'EUR':
                return '€' . number_format($amount, 2);
            default:
                return number_format($amount, 2) . ' ' . $currency;
        }
    }
}

if (!function_exists('slugify')) {
    /**
     * 文字列をURL用スラッグに変換
     */
    function slugify(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}

if (!function_exists('calculateTax')) {
    /**
     * 税額計算
     */
    function calculateTax(float $amount, float $taxRate = 0.10): float
    {
        return round($amount * $taxRate, 2);
    }
}

if (!function_exists('isValidEmail')) {
    /**
     * メールアドレス検証
     */
    function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('generateRandomString')) {
    /**
     * ランダム文字列生成
     */
    function generateRandomString(int $length = 10, string $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'): string
    {
        $result = '';
        $max = strlen($chars) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }
        
        return $result;
    }
}