# Lesson 06: ファイル操作

## 学習目標
- ファイルの読み書きができるようになる
- CSVファイルの操作を理解する
- JSON形式でのデータ保存と読み込みを覚える
- ディレクトリ操作の基本を理解する
- ファイル操作のエラーハンドリングを身につける

## ファイル操作とは
PHPではファイルの作成、読み書き、削除などの操作が簡単にできます。データの永続化やログ出力、設定ファイルの読み込みなど様々な場面で使用されます。

## 主要な関数

### 基本的なファイル操作
```php
// ファイルに書き込み
file_put_contents('filename.txt', 'content');

// ファイルから読み込み
$content = file_get_contents('filename.txt');

// ファイルを行ごとに配列として読み込み
$lines = file('filename.txt');

// ファイルに追記
file_put_contents('filename.txt', 'additional content', FILE_APPEND);
```

### ファイルハンドルを使った操作
```php
// ファイルを開く
$handle = fopen('filename.txt', 'r'); // 読み込みモード
$handle = fopen('filename.txt', 'w'); // 書き込みモード
$handle = fopen('filename.txt', 'a'); // 追記モード

// 1行ずつ読み込み
while (($line = fgets($handle)) !== false) {
    echo $line;
}

// ファイルを閉じる
fclose($handle);
```

### CSV操作
```php
// CSV書き込み
$handle = fopen('data.csv', 'w');
fputcsv($handle, ['列1', '列2', '列3']);
fclose($handle);

// CSV読み込み
$handle = fopen('data.csv', 'r');
while (($row = fgetcsv($handle)) !== false) {
    print_r($row);
}
fclose($handle);
```

### JSON操作
```php
// JSON形式で保存
$data = ['name' => '太郎', 'age' => 25];
file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// JSONから読み込み
$json_content = file_get_contents('data.json');
$data = json_decode($json_content, true);
```

## 課題内容

### 1. sample.php を実行してみよう
まず見本コードを実行して、ファイル操作がどのように動作するかを確認してください。

```bash
php sample.php
```

### 2. practice.php を完成させよう
practice.php に書かれているコメントの指示に従って、コードを書いてください。

#### 実装すべき内容
1. 自己紹介文をテキストファイルに書き込み
2. ファイルの読み込みと表示
3. ファイルへの追記
4. CSVファイルの作成
5. CSVファイルの読み込みと表示
6. JSONファイルの作成
7. JSONファイルの読み込みと表示
8. ディレクトリ内容の一覧表示
9. ファイル情報の取得
10. エラーハンドリング

### 3. 完成したら実行してみよう
```bash
php practice.php
```

## ファイル操作モード
- `r`: 読み込み専用
- `w`: 書き込み専用（既存内容を削除）
- `a`: 追記専用
- `r+`: 読み書き両方
- `w+`: 読み書き両方（既存内容を削除）
- `a+`: 読み書き両方（追記）

## 便利な関数
- `file_exists()`: ファイルの存在確認
- `is_file()`: ファイルかどうか判定
- `is_dir()`: ディレクトリかどうか判定
- `filesize()`: ファイルサイズ取得
- `filemtime()`: 最終更新日時取得
- `unlink()`: ファイル削除
- `mkdir()`: ディレクトリ作成
- `rmdir()`: ディレクトリ削除

## エラーハンドリング
```php
// @ でエラーを抑制して戻り値で判定
$content = @file_get_contents('filename.txt');
if ($content === false) {
    echo "ファイル読み込みエラー";
}
```

## 実用例
- 設定ファイルの読み書き
- ログファイルの出力
- CSVデータのインポート/エクスポート
- キャッシュファイルの管理
- アップロードファイルの処理

## セキュリティ注意点
- ユーザーからのファイル名入力は検証する
- 書き込み権限は適切に設定する
- 機密データは暗号化を検討する

## 次のレッスン
ファイル操作ができたら、次はHTMLフォームとの連携について学習しましょう！