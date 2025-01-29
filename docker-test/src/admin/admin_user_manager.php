<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー情報検索</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #e8f4fa; /* 淡い青の背景色 */
        margin: 0;
        padding: 0;
    }
    h1 {
        text-align: center;
        margin-top: 20px;
        color: #004b75; /* 深めの青 */
    }
    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #cce7f0; /* 淡い青の枠線 */
    }
    .form-container {
        text-align: center;
        margin-bottom: 20px;
    }
    .form-container input {
        width: 70%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #a5cbe0; /* 淡い青のボーダー */
        border-radius: 5px;
        margin-right: 10px;
        background-color: #f0f9ff; /* 青系の薄い背景色 */
    }
    .form-container button {
        padding: 10px 20px;
        font-size: 16px;
        background-color: #4a90e2; /* メインの青色 */
        color: #ffffff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .form-container button:hover {
        background-color: #357ab7; /* 少し濃い青 */
    }
    .button-group {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    .button-group button {
        flex: 1;
        margin: 0 5px;
        padding: 10px;
        font-size: 16px;
        background-color: #4a90e2;
        color: #ffffff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .button-group button:hover {
        background-color: #357ab7;
    }
    .user-info {
        margin-top: 20px;
        padding: 20px;
        background: #f0f9ff; /* 青系の薄い背景色 */
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid #cce7f0; /* 淡い青の枠線 */
    }
    .user-info h2 {
        margin-top: 0;
        color: #004b75;
    }
    .user-info p {
        margin: 10px 0;
    }
    .error-message {
        color: #d9534f; /* 赤系のエラーカラー */
        font-weight: bold;
        text-align: center;
    }
    .success-message {
        color: #5cb85c; /* 緑系の成功メッセージカラー */
        font-weight: bold;
        text-align: center;
    }
    .admin-button {
        display: inline-block;
        margin-bottom: 20px;
        padding: 10px 15px;
        background-color: #4a90e2;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .admin-button:hover {
        background-color: #357ab7;
    }
</style>
</head>
<body>
    <header>
        <h1>ユーザー情報検索</h1>
    </header>
    <main>
        <button class="admin-button" onclick="location.href='admin.php'">管理画面に戻る</button>
        <form method="POST" action="">
            <label for="email">メールアドレスを入力してください:</label>
            <input type="email" name="email" id="email" required placeholder="例: example@mail.com">
            <button type="submit">検索</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
            $email = trim($_POST['email']);
            $user = get_user_by_email($db, $email);

            if ($user) {
                echo '<div class="user-info">';
                echo "<h2>ユーザー情報</h2>";
                echo "<p><strong>ユーザーID:</strong> " . htmlspecialchars($user['USER_ID']) . "</p>";
                echo "<p><strong>ユーザー名:</strong> " . htmlspecialchars($user['USER_NAME']) . "</p>";
                echo "<p><strong>メールアドレス:</strong> " . htmlspecialchars($user['EMAIL_ADDRESS']) . "</p>";
                echo "<p><strong>住所:</strong> " . htmlspecialchars($user['ADDRESS']) . "</p>";
                echo "<p><strong>役割:</strong> " . htmlspecialchars($user['ROLE']) . "</p>";
                echo "<p><strong>性別:</strong> " . htmlspecialchars($user['GENDER']) . "</p>";
                echo "<p><strong>生年月日:</strong> " . htmlspecialchars($user['DATE_OF_BIRTH']) . "</p>";
                echo '<form method="POST" action="">
                        <input type="hidden" name="delete_email" value="' . htmlspecialchars($email) . '">
                        <button type="submit" class="button">ユーザーを削除</button>
                      </form>';
                echo '</div>';
            } else {
                echo '<div class="user-info">';
                echo "<h2>ユーザーが見つかりません</h2>";
                echo "<p>指定されたメールアドレスのユーザー情報は存在しません。</p>";
                echo '</div>';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_email'])) {
            $delete_email = trim($_POST['delete_email']);
            $delete_result = delete_user_by_email($db, $delete_email);

            if ($delete_result) {
                echo '<div class="user-info">';
                echo "<h2>削除成功</h2>";
                echo "<p>ユーザーが正常に削除されました。</p>";
                echo '</div>';
            } else {
                echo '<div class="user-info">';
                echo "<h2>削除失敗</h2>";
                echo "<p>ユーザーの削除に失敗しました。再度お試しください。</p>";
                echo '</div>';
            }
        }
        ?>
    </main>
</body>
</html>
