<?php include_once(__DIR__ . '/../inc/is_hotel.php');
include_once(__DIR__ . '/../inc/db.php');
include_once(__DIR__ . '/../inc/get_url.php');
$db = db_connect();

if ($db === false) {

    $url =  get_url();
    header('Location: ' . $url . '/err.php?err_msg=DB接続エラーです');
    exit;
}

// ホテルプラン追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = $_POST['plan-name'];
    $plan_description = $_POST['plan-description'];
    $max_people = $_POST["max-people"];
    $plan_price = $_POST['plan-price'];
    $child_price = $_POST['child-price'];
    $infant_price = $_POST['infant-price'];
    $room_id = $_POST['room-info'];
    $plan_meal = $_POST['plan-meal'];
    $hotel_id = $_SESSION['hotel_id'];

    $sql = 'INSERT INTO PLAN (PLAN_NAME, MAX_PEOPLE, PLAN_EXPLAIN, CHARGE, CHILD_CHARGE, INFANT_CHARGE, ROOM_ID, HOTEL_ID, EAT) VALUES (:plan_name, :max_people, :plan_description, :plan_price, :child_price, :infant_price, :room_id, :hotel_id, :plan_meal)';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':plan_name', $plan_name);
    $stmt->bindParam(':max_people', $max_people);
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
<?php include_once __DIR__ . '/../inc/header.php'; ?>
<head>
    <link rel="stylesheet" href="./css/hotel_add_plan.css">
</head>
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
                <label for="max-people">最大人数:</label>
                <input type="number" id="max-people" name="max-people" placeholder="例: 4" required min="1">
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
            <div class="add-button">
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
<?php include_once __DIR__ . '/../inc/footer.php'; ?>