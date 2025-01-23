<?php
session_start();

$host = 'mysql.pokapy.com:3307';
$dbname = 'php-docker-db';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('データベース接続に失敗しました: ' . $e->getMessage());
}

// セッションからログイン中のユーザーIDを取得
if (!isset($_SESSION['user_id'])) {
    die('ログインしていません。ログインページに戻ってください。');
}

$user_id = $_SESSION['user_id'];

// 初期表示: データベースから現在の登録情報を取得
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM USER WHERE USER_ID = :user_id');
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die('ユーザー情報が見つかりませんでした。');
    }
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送信されたデータを取得
    $name = trim($_POST['name']);
    $birthdate = trim($_POST['birthdate']);
    $newEmail = trim($_POST['email']);
    $address = trim($_POST['address']);
    $gender = trim($_POST['gender']);

    // 必須項目のチェック
    if (empty($name) || empty($birthdate) || empty($newEmail) || empty($address) || empty($gender)) {
        die('すべての項目を入力してください。');
    }

    // メールアドレスが変更された場合の重複チェック
    if ($newEmail !== $user['EMAIL_ADDRESS']) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM USER WHERE EMAIL_ADDRESS = :email');
        $stmt->bindParam(':email', $newEmail, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            die('このメールアドレスは既に登録されています。');
        }
    }

    // データベースを更新
    $stmt = $pdo->prepare('
        UPDATE USER 
        SET USER_NAME = :name, 
            DATE_OF_BIRTH = :birthdate, 
            EMAIL_ADDRESS = :newEmail, 
            ADDRESS = :address, 
            GENDER = :gender 
        WHERE USER_ID = :user_id
    ');
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':birthdate', $birthdate, PDO::PARAM_STR);
    $stmt->bindParam(':newEmail', $newEmail, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo '<script>alert("更新が完了しました！"); window.location.href = "mypage.html";</script>';
        exit;
    } else {
        die('更新に失敗しました。');
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録情報更新</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e1f5fe; /* 背景色 (青系) */
        }
        .container {
            margin: 60px auto;
            max-width: 800px; /* 幅を広げる */
            padding: 30px; /* 内側の余白を広げる */
            background-color: #ffffff; /* 背景色 (白) */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* 影 (黒) */
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-group label {
            width: 40%;
            font-weight: bold;
            color: #333333; /* ラベルの文字色 (黒) */
        }
        .form-group input {
            flex: 1;
            padding: 12px;
            border: 1px solid #90caf9; /* 入力欄の枠線 (青系) */
            border-radius: 5px;
            font-size: 16px;
            color: #333; /* 文字色 (黒) */
            background-color: #ffffff; /* 入力欄の背景色 (白) */
        }
        .form-group input:focus {
            border-color: #1e88e5; /* フォーカス時の枠線 (青系) */
            background-color: #ffffff; /* フォーカス時背景色 (白) */
        }
        .buttons {
            text-align: center;
            margin-top: 20px;
        }
        .buttons button {
            padding: 12px 24px;
            font-size: 18px;
            background-color: #1976d2; /* ボタン背景色 (青系) */
            color: #ffffff; /* ボタン文字色 (白) */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .buttons button:hover {
            background-color: #1565c0; /* ホバー時の色 (青系) */
        }
    </style>
</head>
<body>
    <h1 class="title">登録情報更新</h1>
    <div class="container">
        <form method="post" action="">
            <div class="form-group">
                <label for="name">氏名:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['USER_NAME'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="birthdate">生年月日:</label>
                <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($user['DATE_OF_BIRTH'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">メール:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['EMAIL_ADDRESS'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="address">住所:</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['ADDRESS'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>性別:</label>
                <div>
                    <input type="radio" id="male" name="gender" value="male" <?= ($user['GENDER'] ?? '') === 'male' ? 'checked' : '' ?>>
                    <label for="male">男</label>
                    <input type="radio" id="female" name="gender" value="female" <?= ($user['GENDER'] ?? '') === 'female' ? 'checked' : '' ?>>
                    <label for="female">女</label>
                </div>
            </div>
            <div class="buttons">
                <button type="submit">更新</button>
            </div>
        </form>
    </div>
</body>
</html>

