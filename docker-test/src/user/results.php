<?php
session_start();
include_once(__DIR__ . '/../inc/db.php');
$pdo = db_connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$conditions = [];
$params = [];

if (!empty($_POST['prefecture'])) {
    $conditions[] = "HOTEL.PREFECTURE LIKE :prefecture";
    $params[':prefecture'] = '%' . $_POST['prefecture'] . '%';
}

if (!empty($_POST['max_people'])) {
    $conditions[] = "PLAN.MAX_PEOPLE <= :max_people";
    $params[':max_people'] = $_POST['max_people'];
}

if (!empty($_POST['charge'])) {
    $conditions[] = "PLAN.CHARGE <= :charge";
    $params[':charge'] = $_POST['charge'];
}

if (isset($_POST['eat'])) {
    $conditions[] = "PLAN.EAT = :eat";
    $params[':eat'] = $_POST['eat'];
}

if (!empty($_POST['bed_number'])) {
    $conditions[] = "ROOM.BED_NUMBER = :bed_number";
    $params[':bed_number'] = $_POST['bed_number'];
}

foreach (['bathroom', 'dryer', 'tv', 'wi_fi', 'pet', 'smoking', 'refrigerator'] as $field) {
    if (isset($_POST[$field])) {
        $conditions[] = "ROOM." . strtoupper($field) . " = :" . $field;
        $params[':' . $field] = $_POST[$field];
    }
}

$query = "SELECT 
            HOTEL.HOTEL_ID AS hotel_id,
            HOTEL.HOTEL_NAME AS hotel_name, 
            PLAN.PLAN_ID AS plan_id,
            PLAN.PLAN_NAME AS plan_name, 
            PLAN.PLAN_EXPLAIN AS plan_explain,
            ROOM.ROOM_PHOTO AS room_photo
          FROM HOTEL 
          LEFT JOIN PLAN ON HOTEL.HOTEL_ID = PLAN.HOTEL_ID
          LEFT JOIN ROOM ON PLAN.ROOM_ID = ROOM.ROOM_ID";

if ($conditions) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include_once(__DIR__ . '/../inc/header.php'); ?>
<link rel="stylesheet" href="result_style.css">

<body>
    <h1>検索結果</h1>
    <?php if ($results): ?>
        <table border="1">
            <tr>
                <th>ホテル名</th>
                <th>プラン名</th>
                <th>プラン説明</th>
                <th>部屋の写真</th>
                <th>詳細</th>
            </tr>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['hotel_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['plan_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['plan_explain'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if (!empty($row['room_photo'])): ?>
                            <img src="/uploads/room/<?= htmlspecialchars($row['room_photo'], ENT_QUOTES, 'UTF-8') ?>" alt="部屋の写真" width="150">
                        <?php else: ?>
                            画像なし
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="detail.php" method="post">
                            <input type="hidden" name="plan_id" value="<?= htmlspecialchars($row['plan_id'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit">詳細を見る</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="no-result">該当する結果がありません。</p>
    <?php endif; ?>
<?php include_once(__DIR__ . '/../inc/footer.php'); ?>
