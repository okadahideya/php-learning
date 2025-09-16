# Lesson 10: エラーハンドリング

## 学習目標
- try-catch文を使った例外処理ができるようになる
- カスタム例外クラスを作成できるようになる
- 適切なエラーログの記録方法を理解する
- finally文の使い方を覚える
- 実用的なエラーハンドリング戦略を身につける

## エラーハンドリングとは
プログラム実行中に発生するエラーや例外的な状況を適切に処理し、プログラムの安定性を保つ技術です。

## 基本構文

### try-catch文
```php
try {
    // エラーが発生する可能性のあるコード
    $result = riskyOperation();
} catch (Exception $e) {
    // エラー処理
    echo "エラー: " . $e->getMessage();
}
```

### 複数の例外タイプ
```php
try {
    // 処理
} catch (InvalidArgumentException $e) {
    // 引数エラーの処理
} catch (FileNotFoundException $e) {
    // ファイル未発見エラーの処理
} catch (Exception $e) {
    // その他のエラーの処理
}
```

### finally文
```php
try {
    // 処理
} catch (Exception $e) {
    // エラー処理
} finally {
    // 必ず実行される処理（クリーンアップなど）
}
```

## 例外の投げ方
```php
function validate($value) {
    if (empty($value)) {
        throw new InvalidArgumentException("値が空です");
    }
}
```

## 主要な例外クラス
- `Exception`: 基本例外クラス
- `InvalidArgumentException`: 不正な引数
- `OutOfRangeException`: 範囲外の値
- `RuntimeException`: 実行時エラー
- `LogicException`: ロジックエラー

## 課題内容

### 1. sample.php を実行してみよう
```bash
php sample.php
```

### 2. practice.php を完成させよう

#### 実装すべき内容
1. ゼロ除算エラーを処理する割り算関数
2. 配列の存在しないインデックスアクセスエラー処理
3. ファイル操作エラーの処理
4. カスタム例外クラス「EmailException」の作成
5. メール送信バリデーション関数
6. finally文を使った複数ファイル処理関数
7. エラーログ記録関数

### 3. 完成したら実行してみよう
```bash
php practice.php
```

## カスタム例外クラス
```php
class CustomException extends Exception {
    private $errorCode;
    
    public function __construct($message, $errorCode = 0) {
        parent::__construct($message);
        $this->errorCode = $errorCode;
    }
    
    public function getErrorCode() {
        return $this->errorCode;
    }
}
```

## エラーログの記録
```php
function logError($message, $context = []) {
    $timestamp = date('Y-m-d H:i:s');
    $log = "[{$timestamp}] {$message}";
    
    if (!empty($context)) {
        $log .= " | " . json_encode($context);
    }
    
    file_put_contents('error.log', $log . "\n", FILE_APPEND);
}
```

## エラーハンドリングのベストプラクティス

### 1. 適切な例外タイプを使用
- 状況に応じて適切な例外クラスを選択
- 必要に応じてカスタム例外を作成

### 2. エラーメッセージは分かりやすく
```php
// 良い例
throw new Exception("ファイル '{$filename}' が見つかりません");

// 悪い例  
throw new Exception("エラー");
```

### 3. エラー情報の記録
```php
catch (Exception $e) {
    error_log("エラー発生: " . $e->getMessage());
    // ユーザーには分かりやすいメッセージを表示
    echo "処理中にエラーが発生しました。";
}
```

### 4. リソースのクリーンアップ
```php
$file = null;
try {
    $file = fopen('data.txt', 'r');
    // ファイル処理
} finally {
    if ($file) {
        fclose($file);
    }
}
```

## 実用的なエラーハンドリング

### Webアプリケーションでの例外処理
```php
try {
    // ビジネスロジック
    $result = processUserData($_POST);
    echo "処理が完了しました";
} catch (ValidationException $e) {
    echo "入力エラー: " . $e->getMessage();
} catch (DatabaseException $e) {
    error_log($e->getMessage());
    echo "システムエラーが発生しました";
}
```

### エラーハンドラーの設定
```php
// 未処理例外のハンドラー
set_exception_handler(function($e) {
    error_log("未処理例外: " . $e->getMessage());
    echo "システムエラーが発生しました";
});
```

## デバッグ情報の取得
```php
try {
    // 処理
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
    echo "ファイル: " . $e->getFile() . "\n";
    echo "行番号: " . $e->getLine() . "\n";
    echo "スタックトレース:\n" . $e->getTraceAsString();
}
```

## まとめ
このレッスンでPHPの基礎学習は完了です！これまでに学んだ内容：

1. **基本構文**: 変数、配列、ループ、関数
2. **オブジェクト指向**: クラス、継承、ファイル分割
3. **実用的な機能**: ファイル操作、フォーム処理
4. **Webアプリ**: セッション、クッキー、データベース
5. **品質管理**: エラーハンドリング

これらの知識を組み合わせて、実際のWebアプリケーション開発に挑戦してみましょう！