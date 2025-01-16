<?php
session_start();
$host = 'mysql.pokapy.com:3307';
$dbname = 'php-docker-db';
$username = 'user'; // データベースユーザー名
$password = 'password'; // データベースパスワード

// データベース接続
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

// $_SERVER['REQUEST_METHOD'] が定義されているか確認
if (!isset($_SERVER['REQUEST_METHOD'])) {
    die("リクエストメソッドが不正です。このスクリプトはWebサーバー経由で実行してください。");
}

// POSTリクエスト以外を拒否
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("このページは直接アクセスできません。フォームからリクエストしてください。");
}

// POSTデータの受け取り
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    die("メールアドレスまたはパスワードを入力してください。");
}


// ユーザー情報の取得
$stmt = $pdo->prepare('SELECT * FROM USER WHERE EMAIL_ADDRESS = :email');
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['USER_PASSWORD'])) {
    $_SESSION['email'] = $user['EMAIL_ADDRESS'];
    header('Location: mypage.html');
    exit;
} else {
    die('ログインに失敗しました。メールアドレスまたはパスワードが間違っています。');
}
?>


