<?php
include_once(__DIR__ . '/../inc/is_hotel.php');
include_once(__DIR__ . '/../inc/db.php');
include_once(__DIR__ . '/../inc/get_url.php');
include_once(__DIR__ . '/../inc/checkFacility.php');

$db = db_connect();
if ($db === false) {
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
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo '<p style="color:red;">削除に失敗しました。</p>';
    }
}

$hotel_id = $_SESSION['hotel_id'];

// ホテルプランの一覧を取得
$sql = 'SELECT * FROM ROOM WHERE hotel_id = :hotel_id';
$stmt = $db->prepare($sql);
$stmt->bindParam(':hotel_id', $hotel_id, PDO::PARAM_INT);
$stmt->execute();
$rooms = $stmt->fetchAll();

include_once(__DIR__ . '/../inc/header.php');
?>

<link rel="stylesheet" href="./css/hotel_room.css">

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
                <div class="details" style="display:none;">
                    <p>ベッド数: <?= htmlspecialchars($room['BED_NUMBER'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>設備:</p>
                    <p>バスルーム: <?= checkFacility($room['BATHROOM']) ?></p>
                    <p>ドライヤー: <?= checkFacility($room['DRYER']) ?></p>
                    <p>テレビ: <?= checkFacility($room['TV']) ?></p>
                    <p>Wi-Fi: <?= checkFacility($room['WI_FI']) ?></p>
                    <p>ペット: <?= checkFacility($room['PET']) ?></p>
                    <p>冷蔵庫: <?= checkFacility($room['REFRIGERATOR']) ?></p>
                    <p>喫煙: <?= checkFacility($room['SMOKING']) ?></p>
                    <img src="<?= '../uploads/room/' . htmlspecialchars($room['ROOM_PHOTO'], ENT_QUOTES, 'UTF-8') ?>" alt="部屋の画像" style="max-width: 100%;">
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
                details.style.display = details.style.display === 'block' ? 'none' : 'block';
            });

            // 削除ボタンがクリックされたときの処理
            deleteButton.addEventListener('click', (event) => {
                event.stopPropagation(); // 親要素のクリックを防止
                const confirmDelete = confirm('本当にこのプランを削除しますか？');
                if (!confirmDelete) {
                    event.preventDefault(); // 削除をキャンセル
                }
            });
        });
    </script>
<?php include_once(__DIR__ . '/../inc/footer.php'); ?>