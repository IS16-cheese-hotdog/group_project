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

// セッションから予約データを取得
if (!isset($_SESSION['reservation'])) {
    header('Location: search.php');
    exit;
}

$reservation = $_SESSION['reservation'];

$user_id = 1; // 仮のユーザーID（本番ではログインユーザーのIDを取得）
$plan_id = $reservation['plan_id'];
$reservation_date = date('Y-m-d'); // 今日の日付
$room_start_date = $reservation['room_start_date'];
$room_end_date = $reservation['room_end_date'];
$adults = $reservation['adults'];
$kids = $reservation['children'];
$infants = $reservation['infants'];

$query = "INSERT INTO RESERVATION (USER_ID, PLAN_ID, RESERVATION_DATE, ROOM_START_DATE, ROOM_END_DATE, ADULT, KID, INFANT)
          VALUES (:user_id, :plan_id, :reservation_date, :room_start_date, :room_end_date, :adults, :kids, :infants)";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':plan_id', $plan_id, PDO::PARAM_INT);
$stmt->bindParam(':reservation_date', $reservation_date, PDO::PARAM_STR);
$stmt->bindParam(':room_start_date', $room_start_date, PDO::PARAM_STR);
$stmt->bindParam(':room_end_date', $room_end_date, PDO::PARAM_STR);
$stmt->bindParam(':adults', $adults, PDO::PARAM_INT);
$stmt->bindParam(':kids', $kids, PDO::PARAM_INT);
$stmt->bindParam(':infants', $infants, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo "<h2>予約が完了しました！</h2>";
    echo '<a href="search.php">検索ページに戻る</a>';
    unset($_SESSION['reservation']); // セッションデータを削除
} else {
    echo "<h2>予約に失敗しました</h2>";
}
?>
