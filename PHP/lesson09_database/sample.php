<?php

// データベース操作の基本（SQLite使用）

try {
    // SQLiteデータベースに接続
    $pdo = new PDO('sqlite:sample.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== データベース接続成功 ===\n";
    
    // テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        age INTEGER
    )";
    $pdo->exec($sql);
    echo "usersテーブルを作成しました。\n";
    
    // 既存データを削除（デモのため）
    $pdo->exec("DELETE FROM users");
    
    echo "\n=== データ挿入（INSERT） ===\n";
    
    // データ挿入
    $stmt = $pdo->prepare("INSERT INTO users (name, email, age) VALUES (?, ?, ?)");
    $stmt->execute(['田中太郎', 'tanaka@example.com', 25]);
    $stmt->execute(['佐藤花子', 'sato@example.com', 30]);
    $stmt->execute(['山田次郎', 'yamada@example.com', 28]);
    
    echo "3人のユーザーを追加しました。\n";
    
    echo "\n=== データ取得（SELECT） ===\n";
    
    // 全データ取得
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "全ユーザー一覧:\n";
    foreach ($users as $user) {
        echo "ID: {$user['id']}, 名前: {$user['name']}, 年齢: {$user['age']}歳\n";
    }
    
    // 条件付き取得
    echo "\n年齢30歳以上のユーザー:\n";
    $stmt = $pdo->prepare("SELECT * FROM users WHERE age >= ?");
    $stmt->execute([30]);
    $older_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($older_users as $user) {
        echo "名前: {$user['name']}, 年齢: {$user['age']}歳\n";
    }
    
    echo "\n=== データ更新（UPDATE） ===\n";
    
    // データ更新
    $stmt = $pdo->prepare("UPDATE users SET age = ? WHERE email = ?");
    $stmt->execute([26, 'tanaka@example.com']);
    $affected = $stmt->rowCount();
    echo "{$affected}件のデータを更新しました。\n";
    
    echo "\n=== データ削除（DELETE） ===\n";
    
    // データ削除
    $stmt = $pdo->prepare("DELETE FROM users WHERE age > ?");
    $stmt->execute([29]);
    $deleted = $stmt->rowCount();
    echo "{$deleted}件のデータを削除しました。\n";
    
    echo "\n=== 統計情報 ===\n";
    
    // 統計クエリ
    $stmt = $pdo->query("SELECT COUNT(*) as count, AVG(age) as avg_age FROM users");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "総ユーザー数: {$stats['count']}人\n";
    echo "平均年齢: " . round($stats['avg_age'], 1) . "歳\n";
    
} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage() . "\n";
}

?>