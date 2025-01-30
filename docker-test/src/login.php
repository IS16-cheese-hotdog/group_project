<?php
session_start();
if (isset($_SESSION["admin_id"])) {
    header('Location: /admin/admin_main.php');
    exit;
} else if (isset($_SESSION["hotel_id"])) {
    header('Location: /hotel/hotel_main.php');
    exit;
} else if (isset($_SESSION["user_id"])) {
    header('Location: /user/mypage.php');
    exit;
}
// データベース接続設定
include_once("./inc/db.php");
$pdo = db_connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// CLI経由での実行を防ぐ
if (php_sapi_name() === 'cli') {
    die("このスクリプトはWebサーバーを介して実行してください。");
}

// リクエストメソッドの確認
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームデータの取得とトリム
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'];

    // 入力チェック
    if (empty($email) || empty($password)) {
        die("メールアドレスまたはパスワードを入力してください。");
    }

    // ユーザー情報の取得
    $stmt = $pdo->prepare('SELECT USER.*, EMAIL.EMAIL FROM USER LEFT JOIN EMAIL ON USER.USER_ID = EMAIL.USER_ID WHERE EMAIL.EMAIL = :email');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['USER_PASSWORD'])) {
        // セッションにUSER_IDを保存
        $_SESSION['user_id'] = $user['USER_ID'];
        if ($user['ROLE'] == 1) {
            $_SESSION['admin_id'] = $user['ROLE'];
            // 管理者の場合は管理者ページへリダイレクト
            header('Location: /admin/admin_main.php');
            exit;
        } else if ($user['ROLE'] >= 2) {
            $_SESSION['hotel_id'] = $user['ROLE'];
            // ホテルの場合はホテルページへリダイレクト
            header('Location: /hotel/hotel_main.php');
            exit;
        } else {
            // ログイン成功後にマイページへリダイレクト
            header('Location: /user/mypage.php');
            exit;
        }
    } else {
        // エラーを表示せずにログインページに戻る
        echo '<script>alert("ログインに失敗しました。メールアドレスまたはパスワードが間違っています。"); window.location.href = "login.php";</script>';
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
        <form action="" method="post">
            <label for="email">メールアドレス:</label>
            <input type="email" id="email" name="email" required>
        
            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>
        
            <button type="submit">ログイン</button>
        </form>
        <a class="register-link" href="./user/add.php">新規会員登録はこちら</a>
        <a class="register-link" href="./index.php">メインページに戻る</a>
    </div>
</body>
</html>
