<?php
session_start();

// CSRF対策用のトークン生成
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$form_data = [];

// フォーム送信の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF対策
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = '<div class="error">不正なリクエストです。</div>';
    } else {
        // バリデーション
        $errors = [];
        
        if (empty($_POST['name'])) {
            $errors[] = '名前は必須です。';
        }
        
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = '有効なメールアドレスを入力してください。';
        }
        
        if (empty($_POST['age']) || !is_numeric($_POST['age'])) {
            $errors[] = '年齢は数字で入力してください。';
        }
        
        if (empty($errors)) {
            // 成功時の処理
            $form_data = [
                'name' => htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'),
                'email' => htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'),
                'age' => (int)$_POST['age'],
                'message' => htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'),
                'submitted_at' => date('Y-m-d H:i:s')
            ];
            
            $message = '<div class="success">フォームが送信されました！</div>';
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // 新しいトークン
        } else {
            $message = '<div class="error">エラー: ' . implode(', ', $errors) . '</div>';
        }
    }
}

// GETパラメータの処理
$get_message = '';
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'welcome':
            $get_message = 'ようこそ！';
            break;
        case 'goodbye':
            $get_message = 'さようなら！';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フォーム処理のサンプル</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .success { color: green; background-color: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .info { background-color: #d1ecf1; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>フォーム処理のサンプル</h1>
    
    <?php if ($get_message): ?>
        <div class="info">メッセージ: <?php echo $get_message; ?></div>
    <?php endif; ?>
    
    <div class="info">
        <strong>GETテスト:</strong>
        <a href="?action=welcome">Welcome</a> | 
        <a href="?action=goodbye">Goodbye</a> | 
        <a href="sample.php">Clear</a>
    </div>
    
    <?php echo $message; ?>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="form-group">
            <label for="name">名前 *</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">メールアドレス *</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="age">年齢 *</label>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($_POST['age'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="message">メッセージ</label>
            <textarea id="message" name="message"><?php echo htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        
        <button type="submit">送信</button>
    </form>
    
    <?php if (!empty($form_data)): ?>
        <div class="info">
            <h3>送信されたデータ</h3>
            <p><strong>名前:</strong> <?php echo $form_data['name']; ?></p>
            <p><strong>メール:</strong> <?php echo $form_data['email']; ?></p>
            <p><strong>年齢:</strong> <?php echo $form_data['age']; ?>歳</p>
            <p><strong>メッセージ:</strong> <?php echo nl2br($form_data['message']); ?></p>
            <p><strong>送信時刻:</strong> <?php echo $form_data['submitted_at']; ?></p>
        </div>
    <?php endif; ?>
</body>
</html>