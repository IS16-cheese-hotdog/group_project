<?php
include_once __DIR__ . '/../inc/is_hotel.php';
include_once __DIR__ . '/../inc/db.php';
include_once __DIR__ . '/../inc/checkFacility.php';

$db = db_connect();
if ($db === false) {
    include_once __DIR__ . '/../inc/get_url.php';
    $url = get_url();
    header('Location: ' . $url . '/err.php?err_msg=DB接続エラーです');
    exit;
}

$hotel_id = $_SESSION['hotel_id'];
$whereClause = '';
$params = [':hotel_id' => $hotel_id];

if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
    $whereClause = ' AND ROOM_START_DATE >= :start_date AND ROOM_END_DATE <= :end_date';
    $params[':start_date'] = $startDate;
    $params[':end_date'] = $endDate;
}

$sql = <<<SQL
SELECT 
    R.*, P.PLAN_NAME, P.CHARGE, P.CHILD_CHARGE, P.INFANT_CHARGE, P.EAT
FROM 
    RESERVATION R
LEFT JOIN 
    PLAN P ON R.PLAN_ID = P.PLAN_ID
WHERE 
    P.HOTEL_ID = :hotel_id $whereClause
SQL;

$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$results = $stmt->fetchAll();

include_once __DIR__ . '/../inc/header.php';
?>

<head>
    <link rel="stylesheet" href="./css/hotel_check.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>予約状況</h1>
        </div>
        <div class="date-picker">
            <form method="get">
                <label for="start-date">開始日:</label>
                <input type="date" id="start-date" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <label for="end-date">終了日:</label>
                <input type="date" id="end-date" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit">絞り込む</button>
            </form>
        </div>
        <table class="reservations">
            <thead>
                <tr>
                    <th>予約ID</th>
                    <th>プラン名</th>
                    <th>予約日</th>
                    <th>宿泊日</th>
                    <th>食事</th>
                    <th>大人</th>
                    <th>子供</th>
                    <th>乳幼児</th>
                    <th>合計金額</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($result['RESERVATION_ID'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($result['PLAN_NAME'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($result['RESERVATION_DATE'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($result['ROOM_START_DATE'], ENT_QUOTES, 'UTF-8'); ?> 〜 <?php echo htmlspecialchars($result['ROOM_END_DATE'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(checkFacility($result['EAT']), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($result['ADULT'], ENT_QUOTES, 'UTF-8'); ?>人</td>
                        <td><?php echo htmlspecialchars($result['KID'], ENT_QUOTES, 'UTF-8'); ?>人</td>
                        <td><?php echo htmlspecialchars($result['INFANT'], ENT_QUOTES, 'UTF-8'); ?>人</td>
                        <td><?php echo htmlspecialchars(($result['ADULT'] * $result['CHARGE']) + ($result['KID'] * $result['CHILD_CHARGE']) + ($result['INFANT'] * $result['INFANT_CHARGE']), ENT_QUOTES, 'UTF-8'); ?>円</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include_once __DIR__ . '/../inc/footer.php'; ?>