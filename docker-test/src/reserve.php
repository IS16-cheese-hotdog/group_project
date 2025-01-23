<?php
// セッションを開始
session_start();

// 仮にログイン中のユーザーIDをセッションに格納（テスト用）
// 実際にはログイン処理時にセッションにユーザーIDを保存してください
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // サンプルユーザーID（テスト用）
}

// セッションからユーザーIDを取得
$user_id = $_SESSION['user_id'];

// データベース接続設定
$host = 'mysql.pokapy.com:3307';
$dbname = 'php-docker-db';
$username = 'user'; // データベースユーザー名
$password = 'password'; // データベースパスワード

// エラーメッセージ初期化
$error_message = '';
$success_message = '';

try {
    // データベース接続
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // フォームデータを取得
        $plan_id = $_POST['plan_id'];
        $room_start_date = $_POST['room_start_date'];
        $stay_days = $_POST['stay_days'];
        $adults = $_POST['adults'];
        $kids = $_POST['kids'];
        $infants = $_POST['infants'];
        $reservation_date = date('Y-m-d H:i:s'); // 現在日時を予約日時に設定
        $room_end_date = date('Y-m-d', strtotime($room_start_date . " + $stay_days days"));

        // バリデーション
        if (empty($plan_id) || empty($room_start_date) || empty($stay_days) || empty($adults)) {
            $error_message = '必須項目をすべて入力してください。';
        } elseif ($stay_days <= 0 || $adults <= 0) {
            $error_message = '宿泊日数と大人の人数は1以上である必要があります。';
        } else {
            // データベースに挿入
            $sql = "INSERT INTO RESERVATION 
                        (USER_ID, PLAN_ID, RESERVATION_DATE, ROOM_START_DATE, ROOM_END_DATE, ADULT, KID, INFANT) 
                    VALUES 
                        (:user_id, :plan_id, :reservation_date, :room_start_date, :room_end_date, :adults, :kids, :infants)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':plan_id', $plan_id);
            $stmt->bindParam(':reservation_date', $reservation_date);
            $stmt->bindParam(':room_start_date', $room_start_date);
            $stmt->bindParam(':room_end_date', $room_end_date);
            $stmt->bindParam(':adults', $adults);
            $stmt->bindParam(':kids', $kids);
            $stmt->bindParam(':infants', $infants);

            if ($stmt->execute()) {
                $success_message = "予約が完了しました！<br>
                    ユーザーID: $user_id<br>
                    プランID: $plan_id<br>
                    予約日時: $reservation_date<br>
                    チェックイン日: $room_start_date<br>
                    チェックアウト日: $room_end_date<br>
                    宿泊日数: $stay_days 泊<br>
                    大人: $adults 人<br>
                    子供: $kids 人<br>
                    乳幼児: $infants 人";
            } else {
                $error_message = '予約の保存に失敗しました。';
            }
        }
    }
} catch (PDOException $e) {
    $error_message = 'データベース接続エラー: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約画面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 400px;
        }
        h2 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #b3d8ff;
            border-radius: 5px;
        }
        input:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>予約フォーム</h2>
        <?php if ($error_message): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php elseif ($success_message): ?>
            <div class="message success"><?= $success_message ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="plan_id">プランID</label>
                <input type="number" id="plan_id" name="plan_id" required>
            </div>
            <div class="form-group">
                <label for="room_start_date">チェックイン日</label>
                <input type="date" id="room_start_date" name="room_start_date" required>
            </div>
            <div class="form-group">
                <label for="stay_days">宿泊日数</label>
                <input type="number" id="stay_days" name="stay_days" min="1" required>
            </div>
            <div class="form-group">
                <label for="adults">大人の人数</label>
                <input type="number" id="adults" name="adults" min="1" required>
            </div>
            <div class="form-group">
                <label for="kids">子供の人数</label>
                <input type="number" id="kids" name="kids" min="0" required>
            </div>
            <div class="form-group">
                <label for="infants">乳幼児の人数</label>
                <input type="number" id="infants" name="infants" min="0" required>
            </div>
            <button type="submit">予約する</button>
        </form>
    </div>
</body>
</html>
