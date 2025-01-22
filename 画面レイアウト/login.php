<?php
session_start();

$host = 'mysql.pokapy.com:3307';
$dbname = 'php-docker-db';
$username = 'user'; // 適切なDBユーザー名
$password = 'password'; // 適切なDBパスワード

// データベース接続
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('データベース接続に失敗しました: ' . $e->getMessage());
}

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("このページは直接アクセスできません。フォームからリクエストしてください。");
}

// POSTリクエストの処理
$userId = $_POST['user_id'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($userId) || empty($password)) {
    die("ユーザーIDまたはパスワードを入力してください。");
}

echo "ユーザーID: $userId<br>";
echo "パスワード: $password<br>";



    
    $stmt = $pdo->prepare('SELECT * FROM USER WHERE user_id = :user_id');
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($passwordInput, $user['password'])) {
        // 認証成功
        $_SESSION['user_id'] = $user['user_id'];
        header('Location: mypage.php'); // 適切なページにリダイレクト
        exit;
    } else {
        // 認証失敗
        echo 'ユーザーIDまたはパスワードが間違っています。';
    }
?>
