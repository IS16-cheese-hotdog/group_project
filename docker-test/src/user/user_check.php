<?php
include_once __DIR__ . '/../inc/is_login.php';
include_once __DIR__ . '/../inc/db.php';
include_once __DIR__ . '/../inc/checkFacility.php';

$user_id = $_SESSION["user_id"];
$pdo = db_connect();

try {
    $stmt = $pdo->prepare('SELECT RESERVATION.*, PLAN.PLAN_NAME, PLAN.HOTEL_ID, PLAN.EAT, PLAN.CHARGE, PLAN.CHILD_CHARGE, PLAN.INFANT_CHARGE, HOTEL.HOTEL_NAME, 
                           (SELECT COUNT(*) FROM REVIEW WHERE REVIEW.RESERVATION_ID = RESERVATION.RESERVATION_ID) AS REVIEW_COUNT 
                           FROM RESERVATION 
                           LEFT JOIN PLAN ON PLAN.PLAN_ID = RESERVATION.PLAN_ID 
                           LEFT JOIN HOTEL ON HOTEL.HOTEL_ID = PLAN.HOTEL_ID 
                           WHERE RESERVATION.USER_ID = :user_id');
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('予約情報の取得に失敗しました: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

include_once __DIR__ . '/../inc/header.php';
?>
<link rel="stylesheet" href="./css/user_check.css">

<h1>予約確認</h1>
<table class="reservations">
    <thead>
        <tr>
            <th>予約ID</th>
            <th>ホテル名</th>
            <th>プラン名</th>
            <th>予約日</th>
            <th>宿泊日</th>
            <th>食事</th>
            <th>大人</th>
            <th>子供</th>
            <th>乳幼児</th>
            <th>合計金額</th>
            <th>評価</th>
            <th>キャンセル</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservations as $reservation): ?>
            <?php 
                // 日付の差を計算
                $checkin = new DateTime($reservation["ROOM_START_DATE"]);
                $checkout = new DateTime($reservation["ROOM_END_DATE"]);
                $diff = $checkin->diff($checkout)->days;
                // 合計金額の計算
                $total_price = ($reservation['ADULT'] * $reservation["CHARGE"]) + 
                               ($reservation['KID'] * $reservation["CHILD_CHARGE"]) + 
                               ($reservation['INFANT'] * $reservation["INFANT_CHARGE"]);
                $total_price *= $diff;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($reservation['RESERVATION_ID'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($reservation['HOTEL_NAME'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($reservation['PLAN_NAME'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($reservation['RESERVATION_DATE'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($reservation['ROOM_START_DATE'], ENT_QUOTES, 'UTF-8'); ?> 〜 <?php echo htmlspecialchars($reservation['ROOM_END_DATE'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars(checkFacility($reservation['EAT']), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($reservation['ADULT'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($reservation['KID'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($reservation['INFANT'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars(number_format($total_price), ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <?php if ($reservation['REVIEW_COUNT'] > 0): ?>
                        <p>評価済み</p>
                    <?php else: ?>
                        <form action="review.php" method="post">
                            <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['RESERVATION_ID'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="submit" value="評価する">
                        </form>
                    <?php endif; ?>
                </td>
                <td>
                    <form action="reservation_cancel.php" method="post">
                        <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['RESERVATION_ID'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="submit" value="キャンセル">
                    </form>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
