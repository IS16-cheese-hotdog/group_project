<?php
include_once __DIR__ . '/../inc/is_login.php';
include_once __DIR__ . '/../inc/db.php';
include_once __DIR__ . '/../inc/checkFacility.php';
$db = db_connect();
if ($db === false) {
    include_once __DIR__ . '/../inc/get_url.php';
    $url = get_url();
    header('Location: ' . $url . '/err.php?err_msg=DB接続エラーです');
    exit;
}

// ホテルの予約情報取得
$hotel_id = $_SESSION['hotel_id'];

// デフォルトで全件取得
$whereClause = '';
$params = [':hotel_id' => $hotel_id];

// 絞り込み条件が指定された場合
if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
    $whereClause = ' AND ROOM_START_DATE >= :start_date AND ROOM_END_DATE <= :end_date';
    $params[':start_date'] = $startDate;
    $params[':end_date'] = $endDate;
}

// SQLクエリ
$sql = 'SELECT RESERVATION.* , PLAN_NAME, PLAN.CHARGE, PLAN.CHILD_CHARGE, PLAN.INFANT_CHARGE, PLAN.EAT
        FROM RESERVATION 
        LEFT JOIN PLAN ON RESERVATION.PLAN_ID = PLAN.PLAN_ID 
        WHERE PLAN.HOTEL_ID = :hotel_id' . $whereClause;

$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$results = $stmt->fetchAll();
?>
<?php include_once __DIR__ . '/../inc/header.php'; ?>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #e6f7ff;
        /* ベースカラー */
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 900px;
        margin: 50px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .header h1 {
        color: #005073;
        font-size: 2em;
    }

    .date-picker {
        text-align: center;
        margin-bottom: 20px;
    }

    .date-picker label {
        font-size: 1.1em;
        color: #005073;
    }

    .date-picker input {
        font-size: 1em;
        padding: 8px 12px;
        border: 1px solid #007BFF;
        border-radius: 5px;
        outline: none;
        transition: border-color 0.3s ease;
    }

    .date-picker input:focus {
        border-color: #004d66;
    }

    .reservations {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }

    .reservations th,
    .reservations td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .reservations th {
        background-color: #d9f0ff;
        /* ヘッダー背景 */
        color: #005073;
    }

    .reservations td {
        background-color: #ffffff;
    }

    .reservations tr:nth-child(even) td {
        background-color: #f4fbff;
        /* 偶数行 */
    }

    .reservations tr:hover td {
        background-color: #d9f0ff;
        /* ホバー効果 */
    }

    .back-button {
        position: absolute;
        top: 20px;
        left: 20px;
    }

    .back-button a {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007BFF;
        color: #ffffff;
        border-radius: 5px;
        text-decoration: none;
        font-size: 1em;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s, transform 0.2s;
    }

    .back-button a:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }

    .back-button a:active {
        background-color: #003d80;
        transform: translateY(0);
    }

    .date-picker button {
        background-color: #007BFF;
        /* ボタンの背景色 */
        color: #ffffff;
        /* テキストの色 */
        font-size: 1em;
        /* フォントサイズ */
        padding: 10px 20px;
        /* 内側の余白 */
        border: none;
        /* ボーダーを削除 */
        border-radius: 25px;
        /* 丸みを追加 */
        cursor: pointer;
        /* マウスオーバー時のカーソル変更 */
        box-shadow: 0 4px 6px rgba(0, 123, 255, 0.3);
        /* ボックスシャドウ */
        transition: all 0.3s ease;
        /* アニメーション効果 */
    }

    .date-picker button:hover {
        background-color: #0056b3;
        /* ホバー時の背景色 */
        box-shadow: 0 6px 8px rgba(0, 86, 179, 0.3);
        /* ホバー時のボックスシャドウ */
        transform: translateY(-2px);
        /* ホバー時の位置調整 */
    }

    .date-picker button:active {
        background-color: #003d80;
        /* クリック時の背景色 */
        box-shadow: 0 3px 5px rgba(0, 61, 128, 0.3);
        /* クリック時のボックスシャドウ */
        transform: translateY(0);
        /* クリック時の位置調整 */
    }
</style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>予約状況</h1>
        </div>
        <div class="date-picker">
            <form method="get">
                <label for="start-date">開始日:</label>
                <input type="date" id="start-date" name="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
                <label for="end-date">終了日:</label>
                <input type="date" id="end-date" name="end_date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">
                <button type="submit">絞り込む</button>
            </form>
        </div>
        <table class="reservations">
            <tr>
                <th>予約ID</th>
                <th>プラン名</th>
                <th>予約日</th>
                <th>宿泊日</th>
                <th>食事</th>
                <th>大人</th>
                <th>子供</th>
                <th>乳幼児</th>
                <th>合計金額</th>
            </tr>
            <?php foreach ($results as $result) : ?>
                <tr>
                    <td><?php echo $result['RESERVATION_ID']; ?></td>
                    <td><?php echo $result['PLAN_NAME']; ?></td>
                    <td><?php echo $result['RESERVATION_DATE']; ?></td>
                    <td><?php echo $result['ROOM_START_DATE']; ?> 〜 <?php echo $result['ROOM_END_DATE']; ?></td>
                    <td><?php echo checkFacility($result['EAT']); ?></td>
                    <td><?php echo $result['ADULT']; ?>人</td>
                    <td><?php echo $result['KID']; ?>人</td>
                    <td><?php echo $result['INFANT']; ?>人</td>
                    <td><?php echo ($result["ADULT"] * $result["CHARGE"]) + ($result['KID'] * $result["CHILD_CHARGE"]) + ($result['INFANT'] * $result["INFANT_CHARGE"]); ?>円</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>