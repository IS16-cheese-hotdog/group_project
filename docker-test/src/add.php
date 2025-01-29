<?php
session_start();

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

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            color: #01579b;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-align: center;
            flex-grow: 1;
        }
        .back-button {
            background-color: #0056b3;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 14px;
            white-space: nowrap;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #00408a;
        }
        form {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px 30px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #007BFF;
        }
        input, select, button {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #b3d8ff;
            border-radius: 6px;
            background-color: #f9fcff;
        }
        input:focus, select:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="login.html" class="back-button">戻る</a>
        <h1>会員登録</h1>
    </div>
    <form action="" method="post">
        <label for="name">氏名:</label>
        <input type="text" id="name" name="name" required>
        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" required>
        <label for="address">住所:</label>
        <input type="text" id="address" name="address" required>
        <label for="dob">生年月日:</label>
        <input type="date" id="dob" name="dob" required>
        <label for="gender">性別:</label>
        <select id="gender" name="gender" required>
            <option value="" disabled selected>選択してください</option>
            <option value="male">男性</option>
            <option value="female">女性</option>
        </select>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">送信</button>
    </form>
</body>
</html>

