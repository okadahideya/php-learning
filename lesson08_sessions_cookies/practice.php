<?php
// 課題：以下の指示に従ってセッション・クッキー管理を実装してください

// 1. セッションを開始してください


// 2. 訪問回数をカウントしてセッションに保存してください
// 初回訪問時は1、以降は+1していく


// 3. 最後に訪問した時刻をセッションに保存してください


// 4. クッキーでユーザー名を保存・取得する機能を実装してください
// フォームから送信されたら30日間有効なクッキーに保存


// 5. POSTで送信されたアクションに応じて処理を分岐してください
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 'reset_session': セッションデータをクリア
    // 'set_username': ユーザー名をクッキーに保存
    // 'clear_username': ユーザー名クッキーを削除
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>セッション・クッキー課題</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .section { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 8px 15px; margin: 5px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        .danger { background-color: #dc3545; }
        input[type="text"] { padding: 5px; width: 200px; }
    </style>
</head>
<body>
    <h1>セッション・クッキー管理</h1>
    
    <!-- 6. セッション情報を表示してください -->
    <div class="section">
        <h2>セッション情報</h2>
        <!-- 訪問回数、最後の訪問時刻、セッションIDを表示 -->
        
    </div>
    
    <!-- 7. クッキー情報を表示してください -->
    <div class="section">
        <h2>クッキー情報</h2>
        <!-- 保存されているユーザー名を表示、未設定の場合は「未設定」と表示 -->
        
    </div>
    
    <!-- ユーザー名設定フォーム -->
    <div class="section">
        <h2>ユーザー名設定</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="ユーザー名を入力" 
                   value="<?php echo htmlspecialchars($_COOKIE['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" name="action" value="set_username">保存</button>
            <button type="submit" name="action" value="clear_username" class="danger">削除</button>
        </form>
    </div>
    
    <!-- セッションリセット -->
    <div class="section">
        <h2>セッション管理</h2>
        <form method="POST">
            <button type="submit" name="action" value="reset_session" class="danger">セッションリセット</button>
        </form>
    </div>
    
    <!-- 8. デバッグ情報を表示してください -->
    <div class="section">
        <h2>デバッグ情報</h2>
        <!-- $_SESSION と $_COOKIE の中身を表示 -->
        
    </div>
</body>
</html>