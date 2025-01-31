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
} else if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['delete_hotel_id'])) {
    try {
        $db->beginTransaction();
        $stmt = $db->prepare('DELETE FROM HOTEL WHERE HOTEL_ID = :hotel_id');
        $stmt->bindParam(':hotel_id', $_POST["delete_hotel_id"], PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $db->prepare('DELETE FROM ROOM WHERE HOTEL_ID = :hotel_id');
        $stmt->bindParam(':hotel_id', $_POST['delete_hotel_id'], PDO::PARAM_INT);
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container input {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        .form-container button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .button-group button {
            flex: 1;
            margin: 0 5px;
            padding: 10px;
            font-size: 16px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button-group button:hover {
            background-color: #0056b3;
        }
        .hotel-info {
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .hotel-info h2 {
            margin-top: 0;
            color: #333;
        }
        .hotel-info p {
            margin: 10px 0;
        }
        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
        }
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
        }
    </style>
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
                        <form acrion="" method="POST" style="display: inline-block;">
                            <input type="hidden" name="delete_hotel_id" value="<?php echo $hotel['HOTEL_ID']; ?>">
                            <button type="submit" name="delete_hotel_id">削除</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

<?php include_once(__DIR__ . '/../inc/footer.php'); ?>