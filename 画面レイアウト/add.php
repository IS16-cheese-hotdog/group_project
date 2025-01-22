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

// 会員登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $passwordInput = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];

    if (empty($userName) || empty($email) || empty($address) || empty($passwordInput) || empty($passwordConfirm)) {
        echo 'すべてのフィールドを入力してくだpさい。';
        exit;
    }

    if ($passwordInput !== $passwordConfirm) {
        echo 'パスワードが一致しません。';
        exit;
    }

    // メールアドレスの重複チェック
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM USER WHERE EMAIL_ADDRESS = :email');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo 'このメールアドレスは既に登録されています。';
        exit;
    }

    // パスワードをハッシュ化
    $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);

    // ユーザーをデータベースに挿入
    $stmt = $pdo->prepare('INSERT INTO USER (USER_NAME, USER_PASSWORD, EMAIL_ADDRESS, ADDRESS) 
                           VALUES (:name, :password, :email, :address)');
    $stmt->bindParam(':name', $userName, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo '会員登録が完了しました。';
        header('Location: login.html'); // ログインページにリダイレクト
        exit;
    } else {
        echo '会員登録に失敗しました。';
    }
}
?>