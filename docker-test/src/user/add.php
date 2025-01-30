<?php
session_start();
include_once(__DIR__ . "/../inc/db.php");
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
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM EMAIL WHERE EMAIL = :email');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        die('このメールアドレスは既に登録されています。');
    }

    // パスワードをハッシュ化
    $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);

    // ユーザーをデータベースに挿入
    $pdo->beginTransaction();
    $stmt = $pdo->prepare(
        'INSERT INTO USER (USER_NAME, ADDRESS, DATE_OF_BIRTH, GENDER, USER_PASSWORD) 
        VALUES (:name, :address, :dob, :gender, :password)'
    );
    $stmt->bindParam(':name', $userName, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->execute();

    $user_id = $pdo->lastInsertId();
    // メールアドレスの登録
    $stmt = $pdo->prepare('INSERT INTO EMAIL (EMAIL, USER_ID) VALUES (:email, :user_id)');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // 登録成功時はログインページへリダイレクト
        $pdo->commit();
        $_SESSION['user_id'] = $user_id;
        header('Location: mypage.php');
        exit;
    } else {
        die('会員登録に失敗しました。');
    }
}
?>
<?php include_once(__DIR__ . "/../inc/header.php"); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            margin: 0;
            padding: 0;
        }

        h1 {
            margin: 0;
            font-size: 20px;
            text-align: center;
            flex-grow: 1;
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
        input, select, .submit-button {
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
        button .submit-button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover .submit-button {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1 >会員登録</h1>
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
        <button type="submit" class="submit-button">送信</button>
    </form>
<?php include_once(__DIR__ . "/../inc/footer.php"); ?>