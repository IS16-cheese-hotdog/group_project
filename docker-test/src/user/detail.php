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
// POSTデータがない場合は検索ページにリダイレクト
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['hotel_id'])) {
    header('Location: search.php');
    exit;
}

$hotel_id = $_POST['hotel_id'];

// データベースから詳細情報を取得
$query = "SELECT HOTEL.HOTEL_NAME AS hotel_name, 
HOTEL.ADDRESS AS hotel_address, PLAN.PLAN_ID AS plan_id,
HOTEL.PHONE_NUMBER AS phone_number,
HOTEL.EMAIL AS email, HOTEL.BUILDING_NAME AS building_name, 
HOTEL.HOTEL_EXPLAIN AS hotel_explain, PLAN.CHARGE AS charge,
PLAN.PLAN_NAME AS plan_name, PLAN.PLAN_EXPLAIN AS plan_explain,
PLAN.EAT AS eat, ROOM.WI_FI AS wi_fi, ROOM.PET AS pet,
ROOM.SMOKING AS smoking
          FROM HOTEL 
          LEFT JOIN ROOM ON HOTEL.HOTEL_ID = ROOM.HOTEL_ID 
          LEFT JOIN PLAN ON PLAN.PLAN_ID = ROOM.PLAN_ID
          WHERE HOTEL.HOTEL_ID = :hotel_id";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':hotel_id', $hotel_id, PDO::PARAM_INT);
$stmt->execute();
$detail = $stmt->fetch(PDO::FETCH_ASSOC);

// 詳細情報が存在しない場合の処理
if (!$detail) {
    die('該当するホテルが見つかりませんでした。');
}

// 0/1 を「あり」「なし」に変換する関数
function displayAvailability($value) {
    return $value === "1" ? "あり" : "なし";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>詳細情報</title>
</head>
<body>
    <h1>詳細情報</h1>

    <p><strong>ホテル名:</strong> <?= htmlspecialchars($detail['hotel_name'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>料金:</strong> <?= htmlspecialchars($detail['charge'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>住所:</strong> <?= htmlspecialchars($detail['hotel_address'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>食事:</strong> <?= displayAvailability($detail['eat']) ?></p>
    <p><strong>Wi-Fi:</strong> <?= displayAvailability($detail['wi_fi']) ?></p>
    <p><strong>ペット可:</strong> <?= displayAvailability($detail['pet']) ?></p>
    <p><strong>喫煙:</strong> <?= displayAvailability($detail['smoking']) ?></p>
    <form action="reserve.php" method="post">
        <input type="hidden" name="plan_id" value="<?= htmlspecialchars($row['plan_id'], ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">予約</button>
    </form>
    <a href="search.php">戻る</a>
    
</body>
</html>