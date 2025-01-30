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
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['plan_id'])) {
    header('Location: search.php');
    exit;
}

$plan_id = $_POST['plan_id'];

// データベースから詳細情報を取得
$query = "SELECT HOTEL.HOTEL_NAME AS hotel_name, 
HOTEL.ADDRESS AS hotel_address, PLAN.PLAN_ID AS plan_id,HOTEL.HOTEL_ID AS hotel_id,
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

<?php include_once __DIR__ . '/../inc/header.php'; ?>
<link rel="stylesheet" href="./css/detail.css">

<body>
    <h1>予約情報を入力</h1>
    <p><strong>ホテル名:</strong> <?= htmlspecialchars($detail['hotel_name'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>プラン名:</strong> <?= htmlspecialchars($detail['plan_name'], ENT_QUOTES, 'UTF-8') ?></p>
    <form action="confirm_reservation.php" method="post">
        <input type="hidden" name="hotel_name" value="<?= htmlspecialchars($_POST['hotel_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="plan_name" value="<?= htmlspecialchars($_POST['plan_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="plan_id" value="<?= htmlspecialchars($_POST['plan_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">        
        <input type="hidden" name="hotel_id" value="<?= htmlspecialchars($detail['hotel_id']?? '', ENT_QUOTES, 'UTF-8') ?>">
        <label>チェックイン日 (宿泊開始日): <input type="date" name="room_start_date" required></label><br>
        <label>宿泊終了日: <input type="date" name="room_end_date" required></label><br>
        <label>大人の人数: <input type="number" name="adults" min="1" required></label><br>
        <label>子供の人数: <input type="number" name="children" min="0"></label><br>
        <label>乳幼児の人数: <input type="number" name="infants" min="0"></label><br>
        <button type="submit">確認する</button>
    </form>
</body>
</html>
