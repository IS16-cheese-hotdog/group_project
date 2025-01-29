<?php
session_start();

// データベース接続設定
include_once("./inc/db.php");
$pdo = db_connect();

// CLI経由での実行を防ぐ
if (php_sapi_name() === 'cli') {
    die("このスクリプトはWebサーバーを介して実行してください。");
}

// リクエストメソッドの確認
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームデータの取得とトリム
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 入力チェック
    if (empty($email) || empty($password)) {
        die("メールアドレスまたはパスワードを入力してください。");
    }

    // ユーザー情報の取得
    $stmt = $pdo->prepare('SELECT * FROM USER WHERE EMAIL_ADDRESS = :email');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['USER_PASSWORD'])) {
        // セッションにUSER_IDを保存
        $_SESSION['user_id'] = $user['USER_ID'];

        // ログイン成功後にマイページへリダイレクト
        header('Location: mypage.html');
        exit;
    } else {
        // エラーを表示せずにログインページに戻る
        echo '<script>alert("ログインに失敗しました。メールアドレスまたはパスワードが間違っています。"); window.location.href = "login.html";</script>';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログインページ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 320px;
        }
        .login-container h2 {
            margin-bottom: 25px;
            text-align: center;
            color: #007BFF;
            font-size: 24px;
        }
        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #b3d8ff;
            border-radius: 6px;
            background-color: #f9fcff;
        }
        .login-container input:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .login-container .register-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007BFF;
            text-decoration: none;
            font-size: 14px;
        }
        .login-container .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>ログイン</h2>
        <form action="login.php" method="post">
            <label for="email">メールアドレス:</label>
            <input type="email" id="email" name="email" required>
        
            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>
        
            <button type="submit">ログイン</button>
        </form>
        <a class="register-link" href="add.php">新規会員登録はこちら</a>
        <a class="register-link" href="main.html">メインページに戻る</a>
    </div>
</body>
</html>
