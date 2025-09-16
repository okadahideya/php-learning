<?php
session_start();

// CSRF対策用のトークン生成
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 課題：以下の指示に従ってフォーム処理を実装してください

$message = '';
$form_data = [];

// 1. POSTメソッドでフォームが送信された時の処理を実装してください


// 2. 以下のフィールドのバリデーションを実装してください
//    - name: 必須、50文字以内
//    - email: 必須、有効なメールアドレス形式
//    - phone: 必須、数字とハイフンのみ（例：090-1234-5678）
//    - birthday: 必須、日付形式
//    - prefecture: 必須、選択されていること
//    - comment: 任意、500文字以内


// 3. バリデーションエラーがある場合はエラーメッセージを表示してください


// 4. バリデーションが成功した場合は、データをサニタイズして配列に格納してください


// 5. 送信されたデータをJSONファイル「contact_forms.json」に保存してください


// 6. GETパラメータ「mode」の値に応じてページの表示を変更してください
//    - mode=thanks: お礼メッセージを表示
//    - mode=contact: 通常のフォーム表示（デフォルト）


?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせフォーム - 課題</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="tel"], input[type="date"], select, textarea {
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        textarea { height: 100px; resize: vertical; }
        input[type="checkbox"], input[type="radio"] { margin-right: 5px; }
        button { background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .success { color: green; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .required { color: red; }
        .result { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>お問い合わせフォーム</h1>
    
    <?php
    // 7. GETパラメータに応じた表示制御をここに実装してください
    // mode=thanksの場合はお礼メッセージとフォームを非表示に
    // それ以外はフォームを表示
    ?>
    
    <!-- メッセージ表示エリア -->
    <?php echo $message; ?>
    
    <!-- フォーム本体 -->
    <form method="POST" action="practice.php">
        <!-- 8. CSRF対策のhiddenフィールドを追加してください -->
        
        
        <div class="form-group">
            <label for="name">お名前 <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="" required>
        </div>
        
        <div class="form-group">
            <label for="email">メールアドレス <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="" required>
        </div>
        
        <div class="form-group">
            <label for="phone">電話番号 <span class="required">*</span></label>
            <input type="tel" id="phone" name="phone" value="" placeholder="090-1234-5678" required>
        </div>
        
        <div class="form-group">
            <label for="birthday">生年月日 <span class="required">*</span></label>
            <input type="date" id="birthday" name="birthday" value="" required>
        </div>
        
        <div class="form-group">
            <label for="prefecture">都道府県 <span class="required">*</span></label>
            <select id="prefecture" name="prefecture" required>
                <option value="">選択してください</option>
                <option value="tokyo">東京都</option>
                <option value="osaka">大阪府</option>
                <option value="kanagawa">神奈川県</option>
                <option value="aichi">愛知県</option>
                <option value="hokkaido">北海道</option>
                <option value="fukuoka">福岡県</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="comment">お問い合わせ内容</label>
            <textarea id="comment" name="comment" placeholder="ご質問やご要望をお聞かせください（500文字以内）"></textarea>
        </div>
        
        <div class="form-group">
            <input type="checkbox" id="agreement" name="agreement" value="1" required>
            <label for="agreement" style="display: inline;">個人情報の取り扱いに同意する <span class="required">*</span></label>
        </div>
        
        <button type="submit">送信</button>
    </form>
    
    <!-- 9. 送信されたデータの表示エリア（バリデーション成功時のみ表示） -->
    
    
    <!-- デバッグ用情報 -->
    <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; font-size: 12px;">
        <h4>デバッグ情報</h4>
        <p><strong>REQUEST_METHOD:</strong> <?php echo $_SERVER['REQUEST_METHOD']; ?></p>
        <p><strong>GET:</strong></p>
        <pre><?php var_dump($_GET); ?></pre>
        <p><strong>POST:</strong></p>
        <pre><?php var_dump($_POST); ?></pre>
    </div>
</body>
</html>