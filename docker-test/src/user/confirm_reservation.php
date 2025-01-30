<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: search.php');
    exit;
}

$_SESSION['reservation'] = $_POST;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>予約内容確認</title>
</head>
<body>
    <h1>予約内容確認</h1>
    <p><strong>ホテル名:</strong> <?= htmlspecialchars($_POST['hotel_name'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>プラン名:</strong> <?= htmlspecialchars($_POST['plan_name'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>チェックイン日:</strong> <?= htmlspecialchars($_POST['room_start_date'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>宿泊終了日:</strong> <?= htmlspecialchars($_POST['room_end_date'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>大人の人数:</strong> <?= htmlspecialchars($_POST['adults'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>子供の人数:</strong> <?= htmlspecialchars($_POST['children'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>乳幼児の人数:</strong> <?= htmlspecialchars($_POST['infants'], ENT_QUOTES, 'UTF-8') ?></p>

    <form action="save_reservation.php" method="post">
        <button type="submit">予約を確定する</button>
    </form>
    <a href="reservation.php">戻る</a>
</body>
</html>
