# Next Step 02: フロントエンド基礎

## 学習目標
- HTML/CSS/JavaScriptの基本を理解する
- PHPとJavaScriptの連携方法を習得する
- Ajax通信でサーバーとの非同期通信を実装する
- インタラクティブなWebアプリケーションを作成する

## 前提条件
- PHP基礎学習完了
- HTML/CSSの基本知識（学習しながらでもOK）
- ブラウザの開発者ツールの基本操作

## 学習内容

### 1. HTML/CSS基礎復習
- セマンティックHTML
- CSSレイアウト（Flexbox/Grid）
- レスポンシブデザイン

### 2. JavaScript基礎
- DOM操作
- イベント処理
- 非同期処理（Promise/async-await）

### 3. PHP + JavaScript連携
- Ajax通信
- JSON データのやり取り
- 動的コンテンツ更新

### 4. 実用的なWebアプリ
- ユーザー管理システム
- リアルタイム検索
- フォームバリデーション

## 実行方法

### 基本的なサンプル確認
```bash
cd sample
php -S localhost:8000
# ブラウザで http://localhost:8000 にアクセス
```

### 実践課題
```bash
cd practice
php -S localhost:8001
# ブラウザで http://localhost:8001 にアクセス
```

## 重要な概念

### DOM操作
```javascript
// 要素取得
const element = document.getElementById('myId');
const elements = document.querySelectorAll('.myClass');

// 内容変更
element.textContent = 'New Text';
element.innerHTML = '<strong>Bold Text</strong>';

// イベント追加
element.addEventListener('click', function() {
    console.log('Clicked!');
});
```

### Ajax通信
```javascript
// Fetch API使用
fetch('/api/users')
    .then(response => response.json())
    .then(data => {
        console.log(data);
        updateUI(data);
    })
    .catch(error => console.error('Error:', error));

// POST通信
fetch('/api/users', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({ name: 'John', email: 'john@example.com' })
});
```

### PHP側でのJSON応答
```php
<?php
header('Content-Type: application/json');

// GET リクエスト処理
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $users = [
        ['id' => 1, 'name' => '田中太郎', 'email' => 'tanaka@example.com'],
        ['id' => 2, 'name' => '佐藤花子', 'email' => 'sato@example.com']
    ];
    echo json_encode($users);
}

// POST リクエスト処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    // データ処理
    echo json_encode(['success' => true, 'message' => 'User created']);
}
?>
```

## 実装課題

### 課題1: 基本的なインタラクション
1. ボタンクリックで文字色変更
2. フォーム入力のリアルタイム表示
3. 画像ギャラリー（次/前ボタン）

### 課題2: Ajax通信
1. ユーザー一覧の動的読み込み
2. 検索機能（入力に応じてフィルタリング）
3. いいね機能（非同期でカウント更新）

### 課題3: 実用的なWebアプリ
1. タスク管理アプリ（追加・削除・編集）
2. チャットシステム（簡易版）
3. 商品カタログ（カート機能付き）

## モダンCSS

### Flexbox レイアウト
```css
.container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.card {
    flex: 1;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
```

### CSS Grid
```css
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}
```

### レスポンシブデザイン
```css
/* モバイルファースト */
.container {
    padding: 1rem;
}

/* タブレット以上 */
@media (min-width: 768px) {
    .container {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
}
```

## セキュリティ注意点

### XSS対策
```javascript
// 危険: innerHTML に直接ユーザー入力を設定
element.innerHTML = userInput; // ❌

// 安全: textContent を使用
element.textContent = userInput; // ✅

// またはサニタイズ
function sanitizeHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
```

### CSRF対策
```javascript
// CSRFトークンを含めた送信
const token = document.querySelector('meta[name="csrf-token"]').content;

fetch('/api/data', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    },
    body: JSON.stringify(data)
});
```

## デバッグ・開発ツール

### ブラウザ開発者ツール
- **Elements**: HTML/CSS検証
- **Console**: JavaScript実行・エラー確認
- **Network**: Ajax通信の確認
- **Application**: ローカルストレージ確認

### 便利なJavaScriptデバッグ
```javascript
// コンソール出力
console.log('Debug info:', data);
console.error('Error occurred:', error);
console.table(arrayData); // 配列をテーブル表示

// ブレークポイント
debugger; // この行で実行停止
```

## パフォーマンス最適化

### 効率的なDOM操作
```javascript
// 悪い例: 繰り返しDOM操作
for (let item of items) {
    container.innerHTML += `<div>${item}</div>`; // ❌
}

// 良い例: まとめて操作
const html = items.map(item => `<div>${item}</div>`).join('');
container.innerHTML = html; // ✅
```

### 非同期処理の最適化
```javascript
// 複数のAPI呼び出しを並列実行
const [users, posts, comments] = await Promise.all([
    fetch('/api/users').then(r => r.json()),
    fetch('/api/posts').then(r => r.json()),
    fetch('/api/comments').then(r => r.json())
]);
```

## 次のステップ
フロントエンド基礎をマスターしたら、次はMySQLデータベースの実用的な操作を学習しましょう！