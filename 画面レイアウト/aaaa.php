<?php
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
?>
