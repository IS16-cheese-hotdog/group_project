<?php include_once(__DIR__ . "/../inc/is_login.php"); ?>
<?php include_once(__DIR__ . "/../inc/db.php");
$db = db_connect();
if ($db === false) {
    include_once(__DIR__ . "/../inc/get_url.php");
    $url = get_url();
    header('Location: ' . $url . '/err.php?err_msg=DB接続エラーです');
    exit;
}


// 削除処理

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_room_id'])) {
    $delete_room_id = $_POST['delete_room_id'];
    $delete_sql = 'DELETE FROM ROOM WHERE ROOM_ID = :room_id';
    $delete_stmt = $db->prepare($delete_sql);
    $delete_stmt->bindParam(':room_id', $delete_room_id, PDO::PARAM_INT);
    if ($delete_stmt->execute()) {
        include_once(__DIR__ . "/../inc/get_url.php");
        $url = get_url();
        header('Location: ' . $url . '/hotel/hotel_room.php');
        exit;
    } else {
        echo '<p style="color:red;">削除に失敗しました。</p>';
    }
}

$hotel_id = $_SESSION["hotel_id"];
// ホテルプランの一覧を取得
$sql = 'SELECT * FROM ROOM WHERE hotel_id = :hotel_id';
$stmt = $db->prepare($sql);
$stmt->bindParam(':hotel_id', $hotel_id);
$stmt->execute();
$rooms = $stmt->fetchAll();

include_once(__DIR__ . "/../inc/checkFacility.php");
?>
<?php include_once(__DIR__ . "/../inc/header.php"); ?>

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
    <h1>部屋情報一覧</h1>
    <a href="hotel_add_room.php" class="add-button">部屋情報を追加</a>
    <ul>
        <?php foreach ($rooms as $room): ?>
            <li>
                <p><?= htmlspecialchars($room['ROOM_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="delete_room_id" value="<?= htmlspecialchars($room['ROOM_ID'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="delete-button">削除</button>
                </form>
                <div class="details">
                    <p>ベッド数: <?= htmlspecialchars($room['BED_NUMBER'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>設備:</p>
                    <p>バスルーム: <?= checkFacility($room['BATHROOM']) ?></p>
                    <p>ドライヤー: <?= checkFacility($room['DRYER']) ?></p>
                    <p>テレビ: <?= checkFacility($room['TV']) ?></p>
                    <p>Wi-Fi: <?= checkFacility($room['WI_FI']) ?></p>
                    <p>ペット: <?= checkFacility($room['PET']) ?></p>
                    <p>冷蔵庫: <?= checkFacility($room['REFRIGERATOR']) ?></p>
                    <p>喫煙: <?= checkFacility($room['SMOKING']) ?></p>
                    <img src="<?php echo '/../uploads/room/' . htmlspecialchars($room['ROOM_PHOTO'], ENT_QUOTES, 'UTF-8') ?>" alt="部屋の画像" style="max-width: 100%;">
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <script>
        document.querySelectorAll('li').forEach(item => {
            const details = item.querySelector('.details');
            const deleteButton = item.querySelector('.delete-button');

            // リストアイテムがクリックされたときの処理
            item.addEventListener('click', () => {
                if (details.style.display === 'block') {
                    details.style.display = 'none';
                } else {
                    details.style.display = 'block';
                }
            });

            // 削除ボタンがクリックされたときの処理
            deleteButton.addEventListener('click', (event) => {
                const confirmDelete = confirm('本当にこのプランを削除しますか？');
                if (!confirmDelete) {
                    event.preventDefault(); // 削除をキャンセル
                }
            });
        });
    </script>
</body>

</html>