<?php
session_start();

// データベース接続設定
$host = 'mysql.pokapy.com:3307';
$dbname = 'php-docker-db';
$username = 'user'; // データベースユーザー名
$password = 'password'; // データベースパスワード

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('データベース接続に失敗しました: ' . $e->getMessage());
}

// CLI経由での実行を防ぐ
if (php_sapi_name() === 'cli') {
    die("このスクリプトはWebサーバーを介して実行してください。");
}

// リクエストメソッドの確認
if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("このページは直接アクセスできません。フォームからリクエストしてください。");
}

// POSTデータの受け取り
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
    if ($user['ROLE'] > 1) {
        $_SESSION['hotel_id'] = $user['ROLE'];
        // ホテルの場合はホテルページへリダイレクト
        include_once __DIR__ . '/inc/get_url.php';
        $url = get_url();
        header('Location: ' . $url . '/hotel/hotel_main.php');
        exit;
    }else if ($user['ROLE'] == 1) {
        // 管理者の場合は管理者ページへリダイレクト
        header('Location: admin.html');
        exit;
    } else {
        // ログイン成功後にマイページへリダイレクト
        header('Location: mypage.html');
        exit;
    }
} else {
    // エラーを表示せずにログインページに戻る
    echo '<script>alert("ログインに失敗しました。メールアドレスまたはパスワードが間違っています。"); window.location.href = "login.html";</script>';
    exit;
}
?>