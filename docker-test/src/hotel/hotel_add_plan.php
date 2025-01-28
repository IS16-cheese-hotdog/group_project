<?php include_once __DIR__ . '/../inc/is_login.php';
include_once __DIR__ . '/../inc/db.php';
$db = db_connect();
if ($db === false) {
    include_once __DIR__ . '/../inc/get_url.php';
    header('Location: ' . get_url() . '/err.php?err_msg=DB接続エラーです');
    exit;
}

// ホテルプラン追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = $_POST['plan-name'];
    $plan_description = $_POST['plan-description'];
    $plan_price = $_POST['plan-price'];
    $child_price = $_POST['child-price'];
    $infant_price = $_POST['infant-price'];
    $room_id = $_POST['room-info'];
    $plan_meal = $_POST['plan-meal'];
    $hotel_id = $_SESSION['hotel_id'];

    $sql = 'INSERT INTO PLAN (PLAN_NAME, PLAN_EXPLAIN, CHARGE, CHILD_CHARGE, INFANT_CHARGE, ROOM_ID, HOTEL_ID, EAT) VALUES (:plan_name, :plan_description, :plan_price, :child_price, :infant_price, :room_id, :hotel_id, :plan_meal)';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':plan_name', $plan_name);
    $stmt->bindParam(':plan_description', $plan_description);
    $stmt->bindParam(':plan_price', $plan_price);
    $stmt->bindParam(':child_price', $child_price);
    $stmt->bindParam(':infant_price', $infant_price);
    $stmt->bindParam(':room_id', $room_id);
    $stmt->bindParam(':hotel_id', $hotel_id);
    $stmt->bindParam(':plan_meal', $plan_meal);
    if ($stmt->execute()) {
        header('Location: ' . get_url() . '/hotel/hotel_plan.php');
        exit;
    } else {
        echo '<p style="color:red;">プランの追加に失敗しました。</p>';
    }
}

//部屋一覧取得
$hotel_id = $_SESSION["hotel_id"];
$sql = 'SELECT ROOM_ID,ROOM_NAME FROM ROOM WHERE hotel_id = :hotel_id';
$stmt = $db->prepare($sql);
$stmt->bindParam(':hotel_id', $hotel_id);
$stmt->execute();
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホテルプラン追加</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            /* ライトブルー背景 */
            margin: 0;
            padding: 0;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            font-size: 14px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .title {
            text-align: center;
            color: #007BFF;
            font-size: 24px;
            margin: 60px 0 30px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #007BFF;
        }

        .form-group input,
        .form-group select,
        .form-group textarea,
        .form-group button {
            width: 100%;
            padding: 12px;
            border: 1px solid #b3d8ff;
            border-radius: 6px;
            background-color: #f9fcff;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .buttons {
            text-align: center;
        }

        .buttons button {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .buttons button:hover {
            background-color: #0056b3;
        }

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #ffffff;
            padding: 20px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 10px;
            text-align: center;
        }

        .popup button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 14px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .popup button:hover {
            background-color: #0056b3;
        }

        .review-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #ffffff;
            padding: 20px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 10px;
            text-align: center;
            width: 90%;
            max-width: 500px;
        }

        .review-popup p {
            margin: 10px 0;
        }

        .review-popup button {
            padding: 10px 20px;
            margin: 10px;
            background-color: #28a745;
        }

        .review-popup button:hover {
            background-color: #218838;
        }

        .review-popup .edit-button {
            background-color: #ffc107;
        }

        .review-popup .edit-button:hover {
            background-color: #e0a800;
        }

        .review-popup .delete-button {
            background-color: #dc3545;
        }

        .review-popup .delete-button:hover {
            background-color: #c82333;
        }



        .radio-group {
            display: flex;
            gap: 20px;
            /* ラジオボタン間の余白 */
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            /* ラジオボタンとテキストの間隔 */
        }
    </style>
</head>
<?php include_once __DIR__ . '/../inc/header.php'; ?>

<body>
    <h1 class="title">ホテルプラン追加</h1>
    <div class="container">
        <form id="plan-form" action="" method="post">
            <div class="form-group">
                <label for="plan-name">プラン名:</label>
                <input type="text" id="plan-name" name="plan-name" placeholder="例: お得な朝食付きプラン" required>
            </div>
            <div class="form-group">
                <label for="plan-description">プラン説明:</label>
                <textarea id="plan-description" name="plan-description" rows="4" placeholder="プランの詳細を記入してください" required></textarea>
            </div>
            <div class="form-group">
                <label for="plan-price">大人価格 (円):</label>
                <input type="number" id="plan-price" name="plan-price" placeholder="例: 12000" required min="0">
            </div>
            <div class="form-group">
                <label for="child-price">子供価格 (円):</label>
                <input type="number" id="child-price" name="child-price" placeholder="例: 6000" min="0">
            </div>
            <div class="form-group">
                <label for="infant-price">乳幼児価格 (円):</label>
                <input type="number" id="infant-price" name="infant-price" placeholder="例: 3000" min="0">
            </div>
            <div class="form-group">
                <label for="room-info">部屋情報:</label>
                <select id="room-info" name="room-info" required>
                    <option value="" disabled selected>選択してください</option>
                    <?php while ($room = $stmt->fetch()) : ?>
                        <option value="<?= $room['ROOM_ID'] ?>"><?= htmlspecialchars($room['ROOM_NAME'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="plan-meal">食事:</label>
                <div class="radio-group">
                    <label><input type="radio" name="plan-meal" value="1" required> あり</label>
                    <label><input type="radio" name="plan-meal" value="0" required> なし</label>
                </div>
            </div>
            <div class="buttons">
                <button type="submit" onclick="Check()">追加</button>
            </div>
        </form>
    </div>

    <script>
        function Check() {
            if (window.confirm('この内容で登録しますか？')) {
                document.getElementById('plan-form').submit();
            }
        }
    </script>
</body>

</html>