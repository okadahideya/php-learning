# フロントエンド基礎学習サンプル - タスク管理システム

HTML5/CSS3/JavaScript ES6+を使ったモダンなフロントエンド開発の学習サンプルです。

## 📋 概要

このサンプルアプリケーションでは、以下のフロントエンド技術を学習できます：

- **HTML5**: セマンティックマークアップ
- **CSS3**: Flexbox・Grid・アニメーション
- **JavaScript ES6+**: モダンな記法・DOM操作・非同期処理
- **Ajax通信**: Fetch API・RESTful API連携
- **レスポンシブデザイン**: モバイルファースト設計
- **ユーザビリティ**: ドラッグ&ドロップ・通知システム

## 🚀 セットアップ方法

### 1. ファイル構成確認
```
sample/
├── index.html          # メインHTML
├── js/
│   └── app.js          # JavaScript アプリケーション
└── README.md           # このファイル
```

### 2. 実行方法
```bash
# PHP内蔵サーバーで起動（推奨）
php -S localhost:8000

# または任意のWebサーバーで index.html を開く
```

ブラウザで `http://localhost:8000` にアクセス

## 🎯 主要機能

### 1. タスク管理
- **CRUD操作**: 作成・読み取り・更新・削除
- **カンバンボード**: ドラッグ&ドロップでステータス変更
- **優先度管理**: 高・中・低の3段階
- **期限管理**: 期限切れの自動検出・警告表示

### 2. リアルタイム機能
- **フィルタリング**: 優先度・ステータス・検索による絞り込み
- **ライブ統計**: タスク状況の即座な更新
- **チャート表示**: Chart.jsによる視覚化

### 3. ユーザーエクスペリエンス
- **レスポンシブデザイン**: PC・タブレット・スマホ対応
- **ダークモード**: ライト・ダークテーマ切り替え
- **通知システム**: 成功・エラー・警告メッセージ
- **データ永続化**: ローカルストレージ保存

### 4. データエクスポート
- **JSON出力**: タスクデータのダウンロード
- **日付フォーマット**: 日本語表示対応

## 🔍 学習ポイント

### HTML5 セマンティックマークアップ
```html
<!-- 意味のあるHTML要素の使用 -->
<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4">
        <!-- ナビゲーション内容 -->
    </div>
</nav>

<main class="max-w-7xl mx-auto py-6">
    <!-- メインコンテンツ -->
</main>
```

### CSS3 モダンレイアウト
```css
/* Grid Layout */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

/* Flexbox */
.flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* CSS アニメーション */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
```

### JavaScript ES6+ 機能
```javascript
// Arrow Functions
const getFilteredTasks = () => {
    return tasks.filter(task => {
        const matchesPriority = !priorityFilter || task.priority === priorityFilter;
        return matchesPriority;
    });
};

// Template Literals
const createNotification = (message, type) => {
    return `
        <div class="notification ${type}">
            <i class="fas fa-${getIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;
};

// Destructuring
const { title, description, priority, status } = formData;

// Async/Await
async function syncWithServer() {
    try {
        const response = await fetch('/api/tasks');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Sync error:', error);
    }
}
```

### DOM操作とイベント処理
```javascript
// イベントリスナー設定
document.getElementById('addTaskBtn').addEventListener('click', openTaskModal);

// 動的要素作成
function createTaskCard(task) {
    const card = document.createElement('div');
    card.className = 'task-card';
    card.innerHTML = `
        <h4>${task.title}</h4>
        <p>${task.description}</p>
    `;
    return card;
}

// フォーム処理
function saveTask(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    // 処理...
}
```

### Fetch APIによる非同期通信
```javascript
// GET リクエスト
async function loadTasks() {
    try {
        const response = await fetch('/api/tasks');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const tasks = await response.json();
        return tasks;
    } catch (error) {
        console.error('Failed to load tasks:', error);
    }
}

// POST リクエスト
async function saveTask(taskData) {
    try {
        const response = await fetch('/api/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(taskData)
        });
        return await response.json();
    } catch (error) {
        console.error('Failed to save task:', error);
    }
}
```

### ドラッグ&ドロップAPI
```javascript
function setupDragAndDrop() {
    // ドラッグ開始
    card.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', e.target.dataset.taskId);
    });
    
    // ドロップゾーン
    zone.addEventListener('dragover', (e) => {
        e.preventDefault(); // デフォルト動作を無効化
    });
    
    // ドロップ処理
    zone.addEventListener('drop', (e) => {
        e.preventDefault();
        const taskId = e.dataTransfer.getData('text/plain');
        const newStatus = e.target.dataset.status;
        changeTaskStatus(taskId, newStatus);
    });
}
```

## 🔧 カスタマイズ例

### 1. 新しいフィルター追加
```javascript
// カテゴリフィルター追加
function applyCategoryFilter() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    return tasks.filter(task => 
        !categoryFilter || task.category === categoryFilter
    );
}
```

### 2. アニメーション追加
```css
/* スライドイン効果 */
.slide-in-left {
    animation: slideInLeft 0.3s ease-out;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
```

### 3. WebSocket連携
```javascript
// リアルタイム通信
const socket = new WebSocket('ws://localhost:8080');

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.type === 'task_updated') {
        updateTaskInUI(data.task);
    }
};

function broadcastTaskUpdate(task) {
    socket.send(JSON.stringify({
        type: 'task_update',
        task: task
    }));
}
```

## 📱 レスポンシブ対応

### Tailwind CSSクラス
```html
<!-- レスポンシブグリッド -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- モバイル: 1列、ラージ画面: 3列 -->
</div>

<!-- レスポンシブテキスト -->
<h1 class="text-lg md:text-xl lg:text-2xl">
    <!-- 画面サイズに応じてフォントサイズ変更 -->
</h1>
```

### CSSメディアクエリ
```css
/* タブレット */
@media (max-width: 768px) {
    .kanban-board {
        grid-template-columns: 1fr;
    }
    
    .task-card {
        margin-bottom: 1rem;
    }
}

/* スマートフォン */
@media (max-width: 480px) {
    .container {
        padding: 0.5rem;
    }
    
    .modal {
        width: 95%;
        margin: 10px auto;
    }
}
```

## 🚀 発展学習

このサンプルをベースに以下の機能を追加して学習を深められます：

### 1. 高度なJavaScript
- **Module システム**: ES6 import/export
- **Web Workers**: バックグラウンド処理
- **Service Workers**: オフライン対応
- **Progressive Web App**: PWA化

### 2. 状態管理
- **LocalStorage**: クライアントサイド永続化
- **IndexedDB**: 大容量データ管理
- **State Pattern**: 状態管理パターン

### 3. テスト
- **Jest**: ユニットテスト
- **Cypress**: E2Eテスト
- **Testing Library**: DOM テスト

### 4. ビルドツール
- **Webpack**: モジュールバンドル
- **Vite**: 高速開発サーバー
- **TypeScript**: 型安全性

## 📚 参考資料

- [MDN Web Docs](https://developer.mozilla.org/) - Web技術の公式ドキュメント
- [Chart.js](https://www.chartjs.org/) - グラフライブラリ
- [Tailwind CSS](https://tailwindcss.com/) - ユーティリティファーストCSS
- [Font Awesome](https://fontawesome.com/) - アイコンライブラリ

---

**このサンプルを通じて、モダンなフロントエンド開発の基礎をしっかりと身につけましょう！**