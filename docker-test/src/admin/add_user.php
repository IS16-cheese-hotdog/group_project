<?php
include_once(__DIR__ . "/../inc/is_admin.php");
include_once(__DIR__ . "/../inc/db.php");
$pdo = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // フォームデータの取得とトリム
        $userName = trim(($_POST['name']));
        $email = trim($_POST['email']);
        $address = trim($_POST['address']);
        $dob = trim($_POST['dob']);
        $gender = trim($_POST['gender']);
        $passwordInput = $_POST['password'];

        // バリデーションエラーメッセージ用
        $errors = [];

        // 氏名チェック（1～50文字）
        if (empty($userName) || mb_strlen($userName) > 50) {
            $errors[] = '氏名は1～50文字以内で入力してください。';
        }

        // メールアドレスチェック
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = '正しいメールアドレスを入力してください。';
        }

        // 住所チェック（1～100文字）
        if (empty($address) || mb_strlen($address) > 100) {
            $errors[] = '住所は1～100文字以内で入力してください。';
        }

        // 生年月日チェック（未来の日付は不可）
        if (empty($dob) || strtotime($dob) > time()) {
            $errors[] = '正しい生年月日を入力してください。';
        }

        // 性別チェック（male または female のみ）
        if (!in_array($gender, ['male', 'female'])) {
            $errors[] = '性別を選択してください。';
        }

        // パスワードチェック（8文字以上、英数字混在）
        if (strlen($passwordInput) < 1) {
            $errors[] = 'パスワードは1字以上';
        }

        // エラーがある場合、表示して終了
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p style='color: red;'>$error</p>";
            }
            die();
        }

        // メールアドレスの重複チェック
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM EMAIL WHERE EMAIL = :email');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            die('<p style="color: red;">このメールアドレスは既に登録されています。</p>');
        }

        // パスワードをハッシュ化
        $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);

        // ユーザーをデータベースに挿入
        $stmt = $pdo->prepare(
            'INSERT INTO USER (USER_NAME, ADDRESS, DATE_OF_BIRTH, GENDER, USER_PASSWORD, ROLE) 
            VALUES (:name, :address, :dob, :gender, :password, 0)'
        );
        $stmt->bindParam(':name', $userName, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->execute();

        // ユーザーID取得
        $user_id = $pdo->lastInsertId();

        // EMAIL テーブルに挿入
        $stmt = $pdo->prepare('INSERT INTO EMAIL (EMAIL, USER_ID) VALUES (:email, :user_id)');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $pdo->commit();
        echo '<p style="color: green;">会員登録が完了しました。</p>';
    } catch (Exception $e) {
        $pdo->rollBack();
        echo '<p style="color: red;">エラー: ' . htmlspecialchars($e->getMessage()) . '</p>';
        die('<p style="color: red;">会員登録に失敗しました。</p>');
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