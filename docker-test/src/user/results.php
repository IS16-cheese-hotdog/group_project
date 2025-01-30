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

$conditions = [];
$params = [];

if (!empty($_POST['address'])) {
    $conditions[] = "HOTEL.ADDRESS LIKE :address";
    $params[':address'] = '%' . $_POST['address'] . '%';
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
            PLAN.PLAN_NAME AS plan_name, 
            PLAN.PLAN_EXPLAIN AS plan_explain 
          FROM HOTEL 
          LEFT JOIN PLAN ON HOTEL.HOTEL_ID = PLAN.HOTEL_ID
          LEFT JOIN ROOM ON PLAN.HOTEL_ID = ROOM.HOTEL_ID";

if ($conditions) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="result_style.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>検索結果</title>
</head>

<head>
    <link rel="stylesheet" href="./css/results.css">
</head>

<body>
    <h1>検索結果</h1>
    <?php if ($results): ?>
        <table border="1">
            <tr>
                <th>ホテル名</th>
                <th>プラン名</th>
                <th>プラン説明</th>
                <th>詳細</th>
            </tr>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['hotel_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['plan_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['plan_explain'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <form action="detail.php" method="post">
                            <input type="hidden" name="hotel_id" value="<?= htmlspecialchars($row['hotel_id'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit">詳細を見る</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>該当する結果がありません。</p>
    <?php endif; ?>
</body>
</html>
<?php
var_dump($query);
var_dump($params);
var_dump($results)
?>
