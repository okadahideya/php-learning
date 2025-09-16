<?php
/**
 * フロントエンド学習用 - Mock API
 * JavaScript から呼び出される簡易APIサンプル
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN');

// プリフライトリクエスト対応
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// データファイルパス
$dataFile = __DIR__ . '/data/tasks.json';

// データファイルディレクトリ作成
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

/**
 * JSONデータを読み込み
 */
function loadTasks() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        return [];
    }
    $json = file_get_contents($dataFile);
    return json_decode($json, true) ?: [];
}

/**
 * JSONデータを保存
 */
function saveTasks($tasks) {
    global $dataFile;
    return file_put_contents($dataFile, json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * レスポンス送信
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * エラーレスポンス送信
 */
function sendError($message, $statusCode = 400) {
    sendResponse(['error' => $message], $statusCode);
}

// ルーティング処理
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api.php', '', $path);

// リクエストボディ取得
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            handleGet($path);
            break;
            
        case 'POST':
            handlePost($path, $input);
            break;
            
        case 'PUT':
            handlePut($path, $input);
            break;
            
        case 'DELETE':
            handleDelete($path);
            break;
            
        default:
            sendError('Method not allowed', 405);
    }
} catch (Exception $e) {
    sendError('Internal server error: ' . $e->getMessage(), 500);
}

/**
 * GET リクエスト処理
 */
function handleGet($path) {
    $tasks = loadTasks();
    
    switch ($path) {
        case '/tasks':
            // 全タスク取得
            sendResponse($tasks);
            break;
            
        case '/tasks/stats':
            // 統計情報取得
            $stats = [
                'total' => count($tasks),
                'completed' => count(array_filter($tasks, fn($t) => $t['status'] === 'completed')),
                'in_progress' => count(array_filter($tasks, fn($t) => $t['status'] === 'in_progress')),
                'todo' => count(array_filter($tasks, fn($t) => $t['status'] === 'todo')),
            ];
            $stats['completion_rate'] = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0;
            sendResponse($stats);
            break;
            
        default:
            // 個別タスク取得
            if (preg_match('/\/tasks\/(\d+)/', $path, $matches)) {
                $taskId = (int)$matches[1];
                $task = array_filter($tasks, fn($t) => $t['id'] === $taskId);
                
                if (empty($task)) {
                    sendError('Task not found', 404);
                }
                
                sendResponse(array_values($task)[0]);
            } else {
                sendError('Not found', 404);
            }
    }
}

/**
 * POST リクエスト処理
 */
function handlePost($path, $input) {
    switch ($path) {
        case '/tasks':
            // 新規タスク作成
            if (!$input || !isset($input['title'])) {
                sendError('Title is required');
            }
            
            $tasks = loadTasks();
            
            $newTask = [
                'id' => time() + rand(1, 1000), // 簡易ID生成
                'title' => $input['title'],
                'description' => $input['description'] ?? '',
                'status' => $input['status'] ?? 'todo',
                'priority' => $input['priority'] ?? 'medium',
                'due_date' => $input['due_date'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $tasks[] = $newTask;
            saveTasks($tasks);
            
            sendResponse($newTask, 201);
            break;
            
        default:
            sendError('Not found', 404);
    }
}

/**
 * PUT リクエスト処理
 */
function handlePut($path, $input) {
    if (preg_match('/\/tasks\/(\d+)/', $path, $matches)) {
        $taskId = (int)$matches[1];
        $tasks = loadTasks();
        
        $taskIndex = array_search($taskId, array_column($tasks, 'id'));
        
        if ($taskIndex === false) {
            sendError('Task not found', 404);
        }
        
        // タスク更新
        $task = &$tasks[$taskIndex];
        $task['title'] = $input['title'] ?? $task['title'];
        $task['description'] = $input['description'] ?? $task['description'];
        $task['status'] = $input['status'] ?? $task['status'];
        $task['priority'] = $input['priority'] ?? $task['priority'];
        $task['due_date'] = $input['due_date'] ?? $task['due_date'];
        $task['updated_at'] = date('Y-m-d H:i:s');
        
        saveTasks($tasks);
        sendResponse($task);
    } else {
        sendError('Not found', 404);
    }
}

/**
 * DELETE リクエスト処理
 */
function handleDelete($path) {
    if (preg_match('/\/tasks\/(\d+)/', $path, $matches)) {
        $taskId = (int)$matches[1];
        $tasks = loadTasks();
        
        $taskIndex = array_search($taskId, array_column($tasks, 'id'));
        
        if ($taskIndex === false) {
            sendError('Task not found', 404);
        }
        
        // タスク削除
        array_splice($tasks, $taskIndex, 1);
        saveTasks($tasks);
        
        sendResponse(['message' => 'Task deleted successfully']);
    } else {
        sendError('Not found', 404);
    }
}

/**
 * 初期データ作成（データファイルが存在しない場合）
 */
function createInitialData() {
    $initialTasks = [
        [
            'id' => 1,
            'title' => 'プロジェクト企画書の作成',
            'description' => '新規プロジェクトの企画書を作成する',
            'status' => 'todo',
            'priority' => 'high',
            'due_date' => date('Y-m-d', strtotime('+7 days')),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 2,
            'title' => 'データベース設計',
            'description' => 'ユーザー管理システムのDB設計',
            'status' => 'in_progress',
            'priority' => 'medium',
            'due_date' => date('Y-m-d', strtotime('+3 days')),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 3,
            'title' => 'UI/UXデザイン',
            'description' => 'メインページのデザイン作成',
            'status' => 'completed',
            'priority' => 'medium',
            'due_date' => date('Y-m-d', strtotime('-2 days')),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    saveTasks($initialTasks);
}

// 初期データがない場合は作成
if (!file_exists($dataFile)) {
    createInitialData();
}