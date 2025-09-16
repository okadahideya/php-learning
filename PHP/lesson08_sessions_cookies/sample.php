<?php
session_start();

// セッション・クッキー操作
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'login':
            $_SESSION['user_id'] = 123;
            $_SESSION['username'] = 'テストユーザー';
            break;
            
        case 'logout':
            session_destroy();
            header('Location: sample.php');
            exit;
            
        case 'add_cart':
            $_SESSION['cart'][] = '商品' . rand(1, 10);
            break;
            
        case 'clear_cart':
            unset($_SESSION['cart']);
            break;
    }
}

// クッキー操作
if (isset($_POST['cookie_action'])) {
    switch ($_POST['cookie_action']) {
        case 'set_theme':
            setcookie('theme', $_POST['theme'], time() + 86400); // 1日有効
            break;
            
        case 'clear_cookies':
            setcookie('theme', '', time() - 3600);
            break;
    }
}

$current_theme = $_COOKIE['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>セッション・クッキーのサンプル</title>
    <style>
        body { 
            font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;
            background-color: <?php echo $current_theme === 'dark' ? '#333' : '#fff'; ?>;
            color: <?php echo $current_theme === 'dark' ? '#fff' : '#333'; ?>;
        }
        .section { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 8px 15px; margin: 5px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <h1>セッション・クッキー管理</h1>
    
    <!-- ログイン状態 -->
    <div class="section">
        <h2>ログイン状態</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p>ユーザーID: <?php echo $_SESSION['user_id']; ?></p>
            <p>ユーザー名: <?php echo $_SESSION['username']; ?></p>
            
            <form method="POST" style="display: inline;">
                <button type="submit" name="action" value="logout" class="btn-danger">ログアウト</button>
            </form>
        <?php else: ?>
            <p>ログインしていません</p>
            <form method="POST" style="display: inline;">
                <button type="submit" name="action" value="login" class="btn-primary">ログイン</button>
            </form>
        <?php endif; ?>
    </div>
    
    <!-- ショッピングカート -->
    <div class="section">
        <h2>ショッピングカート</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <p>カート内容:</p>
            <ul>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <li><?php echo $item; ?></li>
                <?php endforeach; ?>
            </ul>
            <form method="POST" style="display: inline;">
                <button type="submit" name="action" value="clear_cart" class="btn-danger">カートクリア</button>
            </form>
        <?php else: ?>
            <p>カートは空です</p>
        <?php endif; ?>
        
        <form method="POST" style="display: inline;">
            <button type="submit" name="action" value="add_cart" class="btn-primary">商品追加</button>
        </form>
    </div>
    
    <!-- クッキー設定 -->
    <div class="section">
        <h2>テーマ設定</h2>
        <p>現在のテーマ: <?php echo $current_theme; ?></p>
        
        <form method="POST">
            <select name="theme">
                <option value="light" <?php echo $current_theme === 'light' ? 'selected' : ''; ?>>ライト</option>
                <option value="dark" <?php echo $current_theme === 'dark' ? 'selected' : ''; ?>>ダーク</option>
            </select>
            <button type="submit" name="cookie_action" value="set_theme" class="btn-primary">設定</button>
        </form>
        
        <form method="POST" style="margin-top: 10px;">
            <button type="submit" name="cookie_action" value="clear_cookies" class="btn-danger">クッキークリア</button>
        </form>
    </div>
    
    <!-- デバッグ情報 -->
    <div class="section">
        <h2>デバッグ情報</h2>
        <p><strong>セッションID:</strong> <?php echo session_id(); ?></p>
        <p><strong>セッションデータ:</strong></p>
        <pre><?php var_dump($_SESSION); ?></pre>
        <p><strong>クッキーデータ:</strong></p>
        <pre><?php var_dump($_COOKIE); ?></pre>
    </div>
</body>
</html>