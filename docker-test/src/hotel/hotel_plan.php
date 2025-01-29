<?php
include_once __DIR__ . '/../inc/is_login.php';
include_once __DIR__ . '/../inc/get_url.php';
include_once __DIR__ . '/../inc/checkFacility.php';
include_once __DIR__ . '/../inc/db.php';

$url = get_url();
$db = db_connect();
if ($db === false) {
    // DB接続エラーの場合
    header('Location: ' . $url . '/err.php?err_msg=DB接続エラーです');
}

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_plan_id'])) {
    $delete_plan_id = $_POST['delete_plan_id'];
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
$sql = 'SELECT PLAN.*, ROOM.ROOM_NAME, ROOM.BED_NUMBER FROM PLAN JOIN ROOM ON PLAN.room_id = ROOM.room_id WHERE PLAN.hotel_id = :hotel_id';
$hotel_id = $_SESSION['hotel_id'];
$stmt = $db->prepare($sql);
$stmt->bindParam(':hotel_id', $hotel_id);
$stmt->execute();
$plans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホテルプラン一覧</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #0056b3;
            /* ブルー系 */
            margin-top: 30px;
        }

        .add-button {
            display: block;
            margin: 20px auto;
            padding: 12px 20px;
            /* 縦のパディングを調整 */
            background: #007bff;
            /* ブルー */
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            /* フォントサイズを少し大きく */
            width: 200px;
            /* 横幅を固定にして大きすぎないように調整 */
            max-width: 100%;
            /* 最大幅は画面に合わせて伸びる */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .add-button:hover {
            background: #0056b3;
            /* ホバー時に濃いブルー */
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: #0056b3;
            /* ブルー系 */
            background: #f1f1f1;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background: #e1e1e1;
        }

        ul {
            list-style-type: none;
            padding: 0;
            max-width: 800px;
            margin: 30px auto;
        }

        li {
            background: #ffffff;
            margin: 15px 0;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #cce5ff;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        li:hover {
            transform: translateY(-5px);
        }

        .details {
            display: none;
            margin-top: 15px;
            background: #f0f8ff;
            padding: 10px;
            border: 1px solid #cce5ff;
            border-radius: 8px;
        }

        .details-button,
        .delete-button {
            position: absolute;
            top: 10px;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s ease;
        }

        .details-button {
            right: 90px;
            background: #007bff;
            /* ブルー系 */
            color: #fff;
        }

        .details-button:hover {
            background: #0056b3;
        }

        .delete-button {
            right: 10px;
            background: #dc3545;
            /* 赤系 */
            color: #fff;
        }

        .delete-button:hover {
            background: #c82333;
        }
    </style>
</head>

<body>
    <button onclick="history.back()" class="back-button">戻る</button>
    <h1>ホテルプラン一覧</h1>
    <a href="hotel_add_plan.php" class="add-button">プランを追加</a>
    <ul>
        <?php foreach ($plans as $plan) : ?>
            <li>
                <p><?= htmlspecialchars($plan['PLAN_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="details">
                    <p>部屋名: <?= htmlspecialchars($plan['ROOM_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>大人料金: ¥<?= htmlspecialchars($plan['CHARGE'], ENT_QUOTES, 'UTF-8') ?>/泊</p>
                    <p>子供料金: ¥<?= htmlspecialchars($plan['CHILD_CHARGE'], ENT_QUOTES, 'UTF-8') ?>/泊</p>
                    <p>乳幼児料金: ¥<?= htmlspecialchars($plan['INFANT_CHARGE'], ENT_QUOTES, 'UTF-8') ?>/泊</p>
                    <p>最大人数: <?= htmlspecialchars($plan['MAX_PEOPLE'], ENT_QUOTES, 'UTF-8') ?>人</p>
                    <p>食事: <?= htmlspecialchars(checkFacility($plan['EAT']), ENT_QUOTES, 'UTF-8') ?></p>
                    <p>説明: <?= htmlspecialchars($plan["PLAN_EXPLAIN"], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="delete_plan_id" value="<?= htmlspecialchars($plan['PLAN_ID'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="delete-button">削除</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
<script>
    document.querySelectorAll('li').forEach(item => {
        const details = item.querySelector('.details');
        const deleteButton = item.querySelector('.delete-button');

        item.addEventListener('click', () => {
            if (details.style.display === 'block') {
                details.style.display = 'none';
            } else {
                details.style.display = 'block';
            }
        });
        deleteButton.addEventListener('click', (event) => {
            const confirmDelete = confirm('本当にこのプランを削除しますか？');
            if (!confirmDelete) {
                event.preventDefault(); // 削除をキャンセル
            }
        });
    });
</script>

</html>