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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['plan_id'])) {
    header('Location: search.php');
    exit;
}

$plan_id = $_POST['plan_id'];

$query = "SELECT HOTEL.HOTEL_NAME AS hotel_name, 
HOTEL.ADDRESS AS hotel_address, PLAN.PLAN_ID AS plan_id, HOTEL.HOTEL_ID AS hotel_id,
HOTEL.PHONE_NUMBER AS phone_number, HOTEL.PREFECTURE AS prefecture,
HOTEL.BUILDING_NAME AS building_name, 
HOTEL.HOTEL_EXPLAIN AS hotel_explain, PLAN.CHARGE AS charge,
PLAN.PLAN_NAME AS plan_name, PLAN.PLAN_EXPLAIN AS plan_explain,
PLAN.EAT AS eat, ROOM.WI_FI AS wi_fi, ROOM.PET AS pet,
ROOM.SMOKING AS smoking
FROM PLAN
LEFT JOIN HOTEL ON HOTEL.HOTEL_ID = PLAN.HOTEL_ID
LEFT JOIN ROOM ON PLAN.HOTEL_ID = ROOM.HOTEL_ID
WHERE PLAN.PLAN_ID = :plan_id";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':plan_id', $plan_id, PDO::PARAM_INT);
$stmt->execute();
$detail = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約画面</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            justify-content: center;
            align-items: center;
            background-color: #e6f7ff;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            width: 80%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }
        .left {
            width: 60%;
            padding: 20px;
            background-color: #ffffff;
        }
        .right {
            width: 40%;
            padding: 20px;
            background-color: #ffffff;
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
            border-radius: 6px;
            background-color: #f9fcff;
            box-sizing: border-box;
        }
        input:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .reserve-button {
            margin-top: 20px;
        }
        .reserve-button button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .reserve-button button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkinDateInput = document.getElementById('room_start_date');
            const checkoutDateInput = document.getElementById('room_end_date');
            const today = new Date().toISOString().split('T')[0]; // 今日の日付をISOフォーマットに変換

            checkinDateInput.setAttribute('min', today); // チェックイン日制限

            // チェックイン日が変更されたときにチェックアウト日の制限を設定
            checkinDateInput.addEventListener('input', function() {
                const checkinDate = checkinDateInput.value;
                checkoutDateInput.setAttribute('min', checkinDate); // チェックイン日以降の日付に制限
            });
        });
    </script>
</head>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約画面</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            justify-content: center;
            align-items: center;
            background-color: #e6f7ff;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            width: 80%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }
        .left {
            width: 60%;
            padding: 20px;
            background-color: #ffffff;
        }
        .right {
            width: 40%;
            padding: 20px;
            background-color: #ffffff;
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
            border-radius: 6px;
            background-color: #f9fcff;
            box-sizing: border-box;
        }
        input:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .reserve-button {
            margin-top: 20px;
        }
        .reserve-button button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .reserve-button button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
    <script>
        function validateDates() {
            const checkinDate = document.getElementById('room_start_date').value;
            const checkoutDate = document.getElementById('room_end_date').value;
            if (checkinDate && checkoutDate && checkinDate === checkoutDate) {
                document.getElementById('error-message').textContent = 'チェックイン日とチェックアウト日が同じです。日付を再確認してください。';
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <!-- 左側（フォーム部分） -->
        <div class="left">
            <h2>予約フォーム</h2>
            <form action="confirm_reservation.php" method="post" onsubmit="return validateDates()">
                <input type="hidden" name="hotel_name" value="<?= htmlspecialchars($detail['hotel_name'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="plan_name" value="<?= htmlspecialchars($detail['plan_name'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="plan_id" value="<?= htmlspecialchars($detail['plan_id'], ENT_QUOTES, 'UTF-8') ?>">        
                <input type="hidden" name="hotel_id" value="<?= htmlspecialchars($detail['hotel_id'], ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="form-group">
                    <label for="room_start_date">チェックイン日</label>
                    <input type="date" id="room_start_date" name="room_start_date" required>
                </div>
                <div class="form-group">
                    <label for="room_end_date">宿泊終了日</label>
                    <input type="date" id="room_end_date" name="room_end_date" required>
                </div>
                <div class="form-group" style="display: flex; justify-content: space-between;">
                    <div style="flex: 1; margin-right: 10px;">
                        <label for="adults">大人</label>
                        <input type="number" id="adults" name="adults" min="1" required>
                    </div>
                    <div style="flex: 1; margin-right: 10px;">
                        <label for="children">子供</label>
                        <input type="number" id="children" name="children" min="0">
                    </div>
                    <div style="flex: 1;">
                        <label for="infants">乳幼児</label>
                        <input type="number" id="infants" name="infants" min="0">
                    </div>
                </div>
                <div id="error-message" class="error-message"></div>
                <div class="form-group reserve-button">
                    <button type="submit">予約を確認</button>
                </div>
            </form>
        </div>

        <!-- 右側（ホテル情報） -->
        <div class="right">
            <h2>ホテル情報</h2>
            <p><strong>ホテル名:</strong> <?= htmlspecialchars($detail['hotel_name'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>プラン名:</strong> <?= htmlspecialchars($detail['plan_name'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>料金:</strong> ¥<?= number_format($detail['charge']) ?> / 泊</p>
            <p><strong>所在地:</strong> <?= htmlspecialchars($detail['hotel_address'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>説明:</strong> <?= nl2br(htmlspecialchars($detail['hotel_explain'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
    </div>
</body>
</html>