<?php
include_once __DIR__ . '/../inc/is_hotel.php';
include_once __DIR__ . '/../inc/get_url.php';
include_once __DIR__ . '/../inc/checkFacility.php';
include_once __DIR__ . '/../inc/db.php';

$url = get_url();
$db = db_connect();
if ($db === false) {
    header('Location: ' . $url . '/err.php?err_msg=DB接続エラーです');
    exit;
}

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_plan_id'])) {
    $delete_plan_id = (int)$_POST['delete_plan_id'];
    $delete_sql = 'DELETE FROM PLAN WHERE plan_id = :plan_id';
    $delete_stmt = $db->prepare($delete_sql);
    $delete_stmt->bindParam(':plan_id', $delete_plan_id, PDO::PARAM_INT);

    if ($delete_stmt->execute()) {
        header('Location: ' . $url . '/hotel_plan.php');
        exit;
    } else {
        echo '<p style="color:red;">削除に失敗しました。</p>';
    }
}

// ホテルプランの一覧を取得
$sql = 'SELECT PLAN.*, ROOM.ROOM_NAME, ROOM.BED_NUMBER 
        FROM PLAN 
        JOIN ROOM ON PLAN.room_id = ROOM.room_id 
        WHERE PLAN.hotel_id = :hotel_id';
$hotel_id = $_SESSION['hotel_id'];
$stmt = $db->prepare($sql);
$stmt->bindParam(':hotel_id', $hotel_id, PDO::PARAM_INT);
$stmt->execute();
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホテルプラン一覧</title>
    <link rel="stylesheet" href="./css/hotel_plan.css">
</head>
<?php include_once __DIR__ . '/../inc/header.php'; ?>
<body>
    <h1>ホテルプラン一覧</h1>
    <a href="hotel_add_plan.php" class="add-button">プランを追加</a>
    <ul>
        <?php foreach ($plans as $plan) : ?>
            <li>
                <p><?= htmlspecialchars($plan['PLAN_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="details">
                    <p>部屋名: <?= htmlspecialchars($plan['ROOM_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>大人料金: &yen;<?= number_format($plan['CHARGE']) ?>/泊</p>
                    <p>子供料金: &yen;<?= number_format($plan['CHILD_CHARGE']) ?>/泊</p>
                    <p>乳幼児料金: &yen;<?= number_format($plan['INFANT_CHARGE']) ?>/泊</p>
                    <p>最大人数: <?= (int)$plan['MAX_PEOPLE'] ?>人</p>
                    <p>食事: <?= htmlspecialchars(checkFacility($plan['EAT']), ENT_QUOTES, 'UTF-8') ?></p>
                    <p>説明: <?= nl2br(htmlspecialchars($plan['PLAN_EXPLAIN'], ENT_QUOTES, 'UTF-8')) ?></p>
                </div>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="delete_plan_id" value="<?= (int)$plan['PLAN_ID'] ?>">
                    <button type="submit" class="delete-button">削除</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <script>
    document.querySelectorAll('li').forEach(item => {
        const details = item.querySelector('.details');
        const deleteButton = item.querySelector('.delete-button');

        item.addEventListener('click', (event) => {
            if (!event.target.closest('button')) { // 削除ボタンのクリックを除外
                details.style.display = details.style.display === 'block' ? 'none' : 'block';
            }
        });

        deleteButton.addEventListener('click', (event) => {
            if (!confirm('本当にこのプランを削除しますか？')) {
                event.preventDefault();
            }
        });
    });
</script>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>
