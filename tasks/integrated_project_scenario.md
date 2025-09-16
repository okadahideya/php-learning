# 統合プロジェクトシナリオ: 「タスク管理システム TaskMaster」

PHP学習の全技術を統合した実践的なプロジェクト開発シナリオです。

## 🎯 プロジェクト概要

### システム概要
**TaskMaster** - チーム向けタスク管理システム

**主要機能:**
- ユーザー管理（登録・認証・プロフィール）
- プロジェクト管理（作成・編集・削除・メンバー招待）
- タスク管理（作成・編集・削除・ステータス管理・優先度設定）
- リアルタイム更新（Ajax・WebSocket）
- レポート機能（進捗可視化・統計情報）
- 通知システム（メール・ブラウザ通知）

### 技術スタック統合
```
Frontend: HTML5/CSS3/JavaScript ES6+
Backend: PHP 8.2+ / Laravel 10
Database: MySQL 8.0
Environment: Docker + docker-compose
Version Control: Git + GitHub
Testing: PHPUnit + Feature Tests
CI/CD: GitHub Actions
```

## 📋 段階別開発スケジュール

### Phase 1: Laravel基礎実装 (next01対応)
**期間**: 20-30時間  
**目標**: 基本的なCRUD機能の実装

#### 1.1 環境構築・プロジェクト初期化
```bash
# Laravel プロジェクト作成
composer create-project laravel/laravel taskmaster
cd taskmaster

# 基本設定
cp .env.example .env
php artisan key:generate
```

#### 1.2 ユーザー認証システム
- Laravel Breeze導入
- ユーザー登録・ログイン・プロフィール編集
- 認証ミドルウェア設定

**実装ファイル:**
```
app/Http/Controllers/Auth/
app/Models/User.php
resources/views/auth/
routes/web.php
```

**学習ポイント:**
- [ ] MVC アーキテクチャの理解
- [ ] Eloquent モデルの活用
- [ ] Blade テンプレートの実装
- [ ] ルーティング・ミドルウェア

#### 1.3 基本的なCRUD機能
**プロジェクト管理:**
```php
// Migration
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->foreignId('owner_id')->constrained('users');
    $table->timestamp('deadline')->nullable();
    $table->enum('status', ['active', 'completed', 'archived']);
    $table->timestamps();
});
```

**タスク管理:**
```php
// Migration
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->foreignId('project_id')->constrained();
    $table->foreignId('assigned_to')->nullable()->constrained('users');
    $table->enum('status', ['todo', 'in_progress', 'review', 'done']);
    $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
    $table->timestamp('due_date')->nullable();
    $table->timestamps();
});
```

**成果物チェック:**
- [ ] プロジェクトの作成・編集・削除ができる
- [ ] タスクの作成・編集・削除ができる
- [ ] ユーザー認証が機能している
- [ ] リレーションが適切に設定されている

### Phase 2: フロントエンド連携 (next02対応)
**期間**: 15-20時間  
**目標**: インタラクティブなUI実装

#### 2.1 レスポンシブUI構築
**技術選択:**
- CSS Framework: Tailwind CSS または Bootstrap
- JavaScript: Vanilla JS + Fetch API
- アイコン: Font Awesome または Heroicons

**実装内容:**
```html
<!-- ダッシュボード画面 -->
<div class="dashboard-container">
    <div class="sidebar">
        <!-- プロジェクト一覧 -->
    </div>
    <div class="main-content">
        <div class="task-board">
            <!-- カンバンボード形式のタスク表示 -->
            <div class="column" data-status="todo">
                <h3>TODO</h3>
                <div class="task-list" id="todo-tasks"></div>
            </div>
            <div class="column" data-status="in_progress">
                <h3>進行中</h3>
                <div class="task-list" id="progress-tasks"></div>
            </div>
            <!-- ... 他のステータス列 -->
        </div>
    </div>
</div>
```

#### 2.2 Ajax機能実装
**リアルタイム更新:**
```javascript
// タスク更新機能
async function updateTaskStatus(taskId, newStatus) {
    try {
        const response = await fetch(`/api/tasks/${taskId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: newStatus })
        });
        
        if (response.ok) {
            const updatedTask = await response.json();
            updateTaskCard(updatedTask);
            showNotification('タスクが更新されました', 'success');
        }
    } catch (error) {
        showNotification('更新に失敗しました', 'error');
    }
}

// ドラッグ&ドロップ機能
function initializeDragAndDrop() {
    const taskCards = document.querySelectorAll('.task-card');
    const taskLists = document.querySelectorAll('.task-list');
    
    taskCards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
    });
    
    taskLists.forEach(list => {
        list.addEventListener('dragover', handleDragOver);
        list.addEventListener('drop', handleDrop);
    });
}
```

**学習ポイント:**
- [ ] CSS Grid/Flexbox でのレイアウト
- [ ] JavaScript イベント処理
- [ ] Fetch API での非同期通信
- [ ] DOM 操作とアニメーション

#### 2.3 API エンドポイント作成
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::get('dashboard/stats', [DashboardController::class, 'getStats']);
});

// app/Http/Controllers/Api/TaskController.php
class TaskController extends Controller
{
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:todo,in_progress,review,done'
        ]);
        
        $task->update(['status' => $request->status]);
        
        return response()->json([
            'message' => 'Task status updated successfully',
            'task' => $task->load('project', 'assignedUser')
        ]);
    }
}
```

### Phase 3: データベース実践 (next03対応)
**期間**: 10-15時間  
**目標**: MySQL最適化と高度なクエリ

#### 3.1 データベース設計最適化
**テーブル関係設計:**
```sql
-- ユーザー・プロジェクト中間テーブル（多対多関係）
CREATE TABLE project_user (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    role ENUM('owner', 'admin', 'member', 'viewer') NOT NULL DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_user (project_id, user_id)
);

-- タスクコメント
CREATE TABLE task_comments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- インデックス最適化
CREATE INDEX idx_tasks_project_status ON tasks(project_id, status);
CREATE INDEX idx_tasks_assigned_due_date ON tasks(assigned_to, due_date);
CREATE INDEX idx_project_user_role ON project_user(user_id, role);
```

#### 3.2 高度なクエリ実装
```php
// app/Models/Project.php
class Project extends Model
{
    // プロジェクトの進捗統計
    public function getProgressStats()
    {
        return $this->tasks()
            ->selectRaw('
                status,
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM tasks WHERE project_id = ?), 2) as percentage
            ')
            ->addBinding($this->id, 'select')
            ->groupBy('status')
            ->get();
    }
    
    // 期限切れタスクの取得
    public function getOverdueTasks()
    {
        return $this->tasks()
            ->where('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->with('assignedUser')
            ->orderBy('due_date')
            ->get();
    }
}

// 複雑な統計クエリ
class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();
        
        $stats = DB::select("
            SELECT 
                p.name as project_name,
                COUNT(t.id) as total_tasks,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN t.due_date < NOW() AND t.status != 'done' THEN 1 ELSE 0 END) as overdue_tasks,
                ROUND(
                    SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) * 100.0 / COUNT(t.id), 
                    2
                ) as completion_rate
            FROM projects p
            INNER JOIN project_user pu ON p.id = pu.project_id
            LEFT JOIN tasks t ON p.id = t.project_id
            WHERE pu.user_id = ?
            GROUP BY p.id, p.name
            ORDER BY completion_rate DESC
        ", [$user->id]);
        
        return response()->json($stats);
    }
}
```

### Phase 4: 開発環境・バージョン管理 (next04対応)
**期間**: 15-20時間  
**目標**: プロダクション対応環境構築

#### 4.1 Docker環境構築
```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - .:/var/www/html
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    ports:
      - "8000:8000"
    depends_on:
      - mysql
      - redis
    environment:
      - APP_ENV=local
      - DB_HOST=mysql
      - REDIS_HOST=redis

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: taskmaster
      MYSQL_USER: taskmaster
      MYSQL_PASSWORD: password
    volumes:
      - mysql_data:/var/lib/mysql
      - ./docker/mysql/init.sql:/docker-entrypoint-initdb.d/init.sql
    ports:
      - "3306:3306"

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

  nginx:
    image: nginx:alpine
    volumes:
      - .:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    ports:
      - "80:80"
    depends_on:
      - app

volumes:
  mysql_data:
```

```dockerfile
# Dockerfile
FROM php:8.2-fpm

# システムの依存関係をインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# PHP拡張機能をインストール
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composer をインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# 権限設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

USER www-data

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

#### 4.2 Git ワークフロー構築
**ブランチ戦略:**
```
main (production)
├── develop (development)
    ├── feature/user-management
    ├── feature/task-crud
    ├── feature/real-time-updates
    └── feature/reporting
```

**GitHub Actions CI/CD:**
```yaml
# .github/workflows/ci.yml
name: CI/CD Pipeline

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: taskmaster_test
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
    
    - name: Copy environment file
      run: cp .env.example .env
    
    - name: Generate application key
      run: php artisan key:generate
    
    - name: Run database migrations
      run: php artisan migrate --env=testing
    
    - name: Run tests
      run: php artisan test
```

### Phase 5: テスト・品質保証 (next05対応)
**期間**: 20-25時間  
**目標**: 包括的テスト実装

#### 5.1 Feature テスト実装
```php
// tests/Feature/ProjectManagementTest.php
class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_create_project()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'Test Project',
            'description' => 'A test project description',
            'deadline' => now()->addMonth()->format('Y-m-d')
        ]);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'owner_id' => $user->id
        ]);
    }
    
    public function test_user_can_only_see_their_projects()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $project1 = Project::factory()->create(['owner_id' => $user1->id]);
        $project2 = Project::factory()->create(['owner_id' => $user2->id]);
        
        $response = $this->actingAs($user1)->get('/api/projects');
        
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['id' => $project1->id])
                ->assertJsonMissing(['id' => $project2->id]);
    }
}

// tests/Feature/TaskManagementTest.php
class TaskManagementTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_task_status_can_be_updated_via_api()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'todo'
        ]);
        
        $response = $this->actingAs($user)
            ->patchJson("/api/tasks/{$task->id}/status", [
                'status' => 'in_progress'
            ]);
        
        $response->assertStatus(200)
                ->assertJsonFragment(['status' => 'in_progress']);
                
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress'
        ]);
    }
}
```

#### 5.2 Unit テスト実装
```php
// tests/Unit/Models/ProjectTest.php
class ProjectTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_project_has_many_tasks()
    {
        $project = Project::factory()->create();
        $tasks = Task::factory()->count(3)->create(['project_id' => $project->id]);
        
        $this->assertInstanceOf(Collection::class, $project->tasks);
        $this->assertCount(3, $project->tasks);
    }
    
    public function test_project_progress_calculation()
    {
        $project = Project::factory()->create();
        
        // 4つのタスクを作成（2つ完了、2つ未完了）
        Task::factory()->count(2)->create([
            'project_id' => $project->id,
            'status' => 'done'
        ]);
        Task::factory()->count(2)->create([
            'project_id' => $project->id,
            'status' => 'todo'
        ]);
        
        $stats = $project->getProgressStats();
        
        $doneStats = $stats->where('status', 'done')->first();
        $this->assertEquals(50.0, $doneStats->percentage);
    }
}
```

#### 5.3 E2E テスト (Laravel Dusk)
```php
// tests/Browser/TaskManagementTest.php
class TaskManagementTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    public function test_user_can_create_and_manage_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        
        $this->browse(function (Browser $browser) use ($user, $project) {
            $browser->loginAs($user)
                   ->visit("/projects/{$project->id}")
                   ->clickLink('新しいタスク')
                   ->type('title', 'Test Task')
                   ->type('description', 'This is a test task')
                   ->select('priority', 'high')
                   ->press('作成')
                   ->assertSee('Test Task')
                   ->assertSee('high');
                   
            // タスクのドラッグ&ドロップテスト
            $browser->drag('.task-card[data-task-id="1"]', '.task-list[data-status="in_progress"]')
                   ->pause(1000)
                   ->assertSee('進行中');
        });
    }
}
```

## 🏆 最終成果物

### プロジェクト完成時の機能
- [ ] **ユーザー管理**: 登録・認証・プロフィール・権限管理
- [ ] **プロジェクト管理**: 作成・編集・メンバー招待・権限設定
- [ ] **タスク管理**: CRUD・ステータス管理・優先度・期限設定
- [ ] **リアルタイム機能**: Ajax更新・ドラッグ&ドロップ
- [ ] **レポート機能**: 進捗統計・ダッシュボード・データ可視化
- [ ] **通知システム**: メール通知・ブラウザ通知
- [ ] **API**: RESTful API・認証・レート制限
- [ ] **テスト**: Unit・Feature・E2E テスト
- [ ] **DevOps**: Docker環境・CI/CD・デプロイ

### 技術習得証明
- **Laravel**: 実用的なWebアプリケーション開発
- **MySQL**: 効率的なデータベース設計・最適化
- **JavaScript**: モダンなフロントエンド開発
- **Docker**: コンテナ化による環境構築
- **Git**: チーム開発ワークフロー
- **Testing**: 品質保証・TDD実践

### ポートフォリオ価値
- **実務レベル**: 企業で通用する技術力の証明
- **統合開発**: 複数技術の連携経験
- **保守性**: テスト・ドキュメント完備
- **スケーラビリティ**: 拡張可能な設計

---

**このプロジェクトの完成により、未経験から実務レベルのPHP開発者への成長を確実に実現できます！**