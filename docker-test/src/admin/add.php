<?php
session_start();

/*
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
*/

include_once("./inc/db.php");
$pdo = db_connect();

// 会員登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームデータの取得とトリム
    $userName = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $passwordInput = $_POST['password'];

    // フィールドの空チェック
    if (empty($userName) || empty($email) || empty($address) || empty($dob) || empty($gender) || empty($passwordInput)) {
        die('すべてのフィールドを入力してください。');
    }

    // メールアドレスの重複チェック
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM USER WHERE EMAIL_ADDRESS = :email');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        die('このメールアドレスは既に登録されています。');
    }

    // パスワードをハッシュ化
    $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);

    // ユーザーをデータベースに挿入
    $stmt = $pdo->prepare(
        'INSERT INTO USER (USER_NAME, EMAIL_ADDRESS, ADDRESS, DATE_OF_BIRTH, GENDER, USER_PASSWORD) 
        VALUES (:name, :email, :address, :dob, :gender, :password)'
    );
    $stmt->bindParam(':name', $userName, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // 登録成功時はログインページへリダイレクト
        header('Location: login.html');
        exit;
    } else {
        die('会員登録に失敗しました。');
    }
}
?>
