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
HOTEL.ADDRESS AS hotel_address, PLAN.PLAN_ID AS plan_id,
HOTEL.PHONE_NUMBER AS phone_number, HOTEL.HOTEL_ID AS hotel_id,
EMAIL.EMAIL AS email, HOTEL.BUILDING_NAME AS building_name, 
HOTEL.HOTEL_EXPLAIN AS hotel_explain, PLAN.CHARGE AS charge,
PLAN.PLAN_NAME AS plan_name, PLAN.PLAN_EXPLAIN AS plan_explain,
PLAN.EAT AS eat, ROOM.WI_FI AS wi_fi, ROOM.PET AS pet,
ROOM.SMOKING AS smoking
FROM PLAN
LEFT JOIN HOTEL ON HOTEL.HOTEL_ID = PLAN.HOTEL_ID
LEFT JOIN ROOM ON PLAN.ROOM_ID = ROOM.ROOM_ID 
LEFT JOIN USER ON USER.ROLE = HOTEL.HOTEL_ID
LEFT JOIN EMAIL ON EMAIL.USER_ID = USER.USER_ID
WHERE PLAN.PLAN_ID = :plan_id";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':plan_id', $plan_id, PDO::PARAM_INT);
$stmt->execute();
$detail = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM PLAN WHERE HOTEL_ID = (SELECT HOTEL_ID FROM PLAN WHERE PLAN_ID = :plan_id)";
$stmt2 = $pdo->prepare($sql); // query() → prepare()
$stmt2->bindValue(":plan_id", $plan_id, PDO::PARAM_INT); // バインド処理を修正
$stmt2->execute();
$plans = $stmt2->fetchAll(PDO::FETCH_ASSOC);
if (!$detail) {
    die('該当するホテルが見つかりませんでした。');
}

function displayAvailability($value)
{
    return $value === "1" ? "あり" : "なし";
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e6f7ff;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .container {
            width: 85%;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        p {
            line-height: 1.6;
            font-size: 16px;
            color: #555;
        }

        .hotel-photos {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .hotel-photos img {
            width: 240px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .hotel-photos img:hover {
            transform: scale(1.05);
        }

        .reservation-button {
            display: block;
            width: 200px;
            text-align: center;
            margin: 20px auto;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .reservation-button:hover {
            background-color: #0056b3;
        }

        .plan-list {
            list-style: none;
            padding: 0;
            margin-top: 30px;
        }

        .plan-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    background-color: #f1f1f1;
    margin-bottom: 15px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.plan-list li:hover {
    background-color: #66b3ff;
    color: white;
    transform: scale(1.02);
}

.plan-list span {
    font-size: 18px;
    color: #333;
    flex: 1;
}

.plan-list .price {
    font-size: 18px;
    font-weight: bold;
    color: #007bff;
    text-align: center;
    min-width: 150px; /* 幅を固定 */
}

.plan-list button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}
    </style>
</head>
<?php include_once(__DIR__ . '/../inc/header.php'); ?>

<body>
    <div class="container">
        <div class="hotel-photos">
            <img src="./../img/IMG_0119.JPG" alt="ホテルの外観">
            <img src="./../img/IMG_2185.JPG" alt="ホテルの内観">
        </div>

        <h2><?= htmlspecialchars($detail['hotel_name'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><strong>プラン名:</strong> <?= htmlspecialchars($detail['plan_name'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>料金:</strong> <?= htmlspecialchars($detail['charge'], ENT_QUOTES, 'UTF-8') ?> 円</p>
        <p><strong>住所:</strong> <?= htmlspecialchars($detail['hotel_address'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>e-mail:</strong> <?= htmlspecialchars($detail['email'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>食事:</strong> <?= displayAvailability($detail['eat']) ?></p>
        <p><strong>Wi-Fi:</strong> <?= displayAvailability($detail['wi_fi']) ?></p>
        <p><strong>ペット可:</strong> <?= displayAvailability($detail['pet']) ?></p>
        <p><strong>喫煙:</strong> <?= displayAvailability($detail['smoking']) ?></p>

        <div class="hotel-info">
            <h2>プラン一覧</h2>
            <ul class="plan-list">
                <?php foreach ($plans as $plan): ?>
                    <li>
                        <span><?= htmlspecialchars($plan['PLAN_NAME'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="price">¥<?= number_format(htmlspecialchars($plan['CHARGE'], ENT_QUOTES, 'UTF-8')) ?></span>
                        <form action="reservation.php" method="post" style="display:inline;">
                            <input type="hidden" name="plan_id" value="<?= htmlspecialchars($plan['PLAN_ID'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit">予約する</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
</body>

</html>
<?php include_once(__DIR__ . '/../inc/footer.php'); ?>