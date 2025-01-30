<?php
session_start();

$host = 'mysql.pokapy.com:3307';
$dbname = 'php-docker-db';
$username = 'user'; // データベースユーザー名
$password = 'password'; // データベースパスワード

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('データベース接続に失敗しました: ' . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: search.php');
    exit;
}

$_SESSION['reservation'] = $_POST;


$query = "SELECT PLAN.PLAN_ID AS plan_id,
        PLAN.PLAN_NAME AS plan_name,
        HOTEL.HOTEL_ID AS hotel_id,
        HOTEL.HOTEL_NAME AS hotel_name
          FROM PLAN
          LEFT JOIN HOTEL ON HOTEL.HOTEL_ID = PLAN.HOTEL_ID
          WHERE PLAN.PLAN_ID = :plan_id";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':plan_id', $_POST["plan_id"], PDO::PARAM_INT);
$stmt->execute();
$detail = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>予約内容確認</title>
</head>
<body>
    <h1>予約内容確認</h1>
    <p><strong>ホテル名:</strong> <?= htmlspecialchars($detail['hotel_name'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>プラン名:</strong> <?= htmlspecialchars($detail['plan_name'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>チェックイン日:</strong> <?= htmlspecialchars($_POST['room_start_date'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>宿泊終了日:</strong> <?= htmlspecialchars($_POST['room_end_date'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>大人の人数:</strong> <?= htmlspecialchars($_POST['adults'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>子供の人数:</strong> <?= htmlspecialchars($_POST['children'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>乳幼児の人数:</strong> <?= htmlspecialchars($_POST['infants'], ENT_QUOTES, 'UTF-8') ?></p>

    <form action="save_reservation.php" method="post">
        <input type="hidden" name="plan_id" value="<?= htmlspecialchars($_POST['plan_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">        
        <input type="hidden" name="hotel_id" value="<?= htmlspecialchars($detail['hotel_id']?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">予約を確定する</button>
    </form>
    <a href="reservation.php">戻る</a>
</body>
</html>
