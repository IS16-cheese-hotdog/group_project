<?php
include_once(__DIR__ ."/../inc/is_admin.php");
include_once(__DIR__ . '/../inc/db.php');

// データベース接続
$db = db_connect();
if ($db === false) {
    // エラーメッセージを表示
    die('<div style="color: red; font-weight: bold;">データベース接続に失敗しました。管理者に連絡してください。</div>');
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $db->query('SELECT HOTEL.*, EMAIL.EMAIL FROM HOTEL LEFT JOIN USER ON HOTEL.HOTEL_ID = USER.ROLE LEFT JOIN EMAIL ON USER.USER_ID = EMAIL.USER_ID');
    $hotels = $stmt->fetchAll();
} else if ($_SERVER['delete_hotel_id'] == 'POST') {
    try {
        $db->beginTransaction();
        $stmt = $db->prepare('DELETE FROM HOTEL WHERE HOTEL_ID = :hotel_id');
        $stmt->bindParam(':hotel_id', $_POST['hotel_id'], PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $db->prepare('DELETE FROM ROOM WHERE HOTEL_ID = :hotel_id');
        $stmt->bindParam(':hotel_id', $_POST['hotel_id'], PDO::PARAM_INT);
        $stmt->execute();

        $db->commit();

        header('Location: ' . $_SERVER['PHP_SELF']);
    } catch (PDOException $e) {
        $db->rollBack();
        echo '<div style="color: red; font-weight: bold;">エラー: ' . $e->getMessage() . '</div>';
    }

}
?>

<?php include_once(__DIR__ . '/../inc/header.php'); ?>

<head>
    <link rel="stylesheet" href="./css/admin_hotel_manager.css">
</head>

</head>
<body>
    <h1>ホテル管理ページ</h1>
    <div class="container">
        <form method="POST" class="form-container">
            <input type="text" name="hotel_name" placeholder="ホテル名を入力してください" required>
            <button type="submit">検索</button>
        </form>
        <div class="button-group">
            <button onclick="location.href='add_hotel.php'">ホテルを追加</button>
            <button onclick="location.href='admin.php'">管理画面に戻る</button>
        </div>

        <?php if (isset($hotels)): ?>
            <?php foreach ($hotels as $hotel): ?>
                <div class="hotel-info">
                    <h2><?php echo $hotel['HOTEL_NAME']; ?></h2>
                    <p>ホテルID: <?php echo $hotel['HOTEL_ID']; ?></p>
                    <p>住所: <?php echo $hotel['ADDRESS']; ?></p>
                    <p>電話番号: <?php echo $hotel['PHONE_NUMBER']; ?></p>
                    <p>メールアドレス: <?php echo $hotel['EMAIL']; ?></p>
                    <div class="button-group">
                        <form action="admin_hotel_update.php" method="POST">
                            <input type="hidden" name="hotel_id" value="<?php echo $hotel['HOTEL_ID']; ?>">
                            <button type="submit">編集</button>
                        </form>
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="hotel_id" value="<?php echo $hotel['HOTEL_ID']; ?>">
                            <button type="submit" name="delete_hotel_id">削除</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

<?php include_once(__DIR__ . '/../inc/footer.php'); ?>