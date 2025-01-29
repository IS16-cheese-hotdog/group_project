<?php
include_once __DIR__ . '/../inc/is_hotel.php';
ob_start();
include_once __DIR__ . '/../inc/db.php';

try {
    $db = db_connect();
    if ($db === false) {
        throw new Exception('Database connection failed.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $hotel_id = $_SESSION['hotel_id'] ?? null;
        $room_name = $_POST['room_name'] ?? null;
        $bed_number = (int) $_POST['bed_number'];
        $bathroom = (int) $_POST['bathroom'];
        $dryer = (int) $_POST['dryer'];
        $tv = (int) $_POST['tv'];
        $wifi = (int) $_POST['wifi'];
        $pet = (int) $_POST['pet'];
        $refrigerator = (int) $_POST['refrigerator'];
        $smoking = (int) $_POST['smoking'];

        // Validate inputs
        if (!$hotel_id || !$room_name) {
            throw new Exception('ホテルIDまたは部屋名が不正です。');
        }

        // File upload handling
        $img = null; // Default value
        if (isset($_FILES['room-photo']) && $_FILES['room-photo']['error'] === UPLOAD_ERR_OK) {
            $img_ext = pathinfo($_FILES['room-photo']['name'], PATHINFO_EXTENSION);
            $img_name = uniqid(mt_rand(), true) . '.' . $img_ext;
            
            $upload_path = __DIR__ . '/../uploads/room/' . $img_name;

            if (!move_uploaded_file($_FILES['room-photo']['tmp_name'], $upload_path)) {
                throw new Exception('ファイルのアップロードに失敗しました。');
            }
            $img = $img_name;
        }

        $sql = "INSERT INTO ROOM 
                (HOTEL_ID, ROOM_NAME, BED_NUMBER, BATHROOM, DRYER, TV, WI_FI, PET, REFRIGERATOR, SMOKING, ROOM_PHOTO) 
                VALUES 
                (:hotel_id, :room_name, :bed_number, :bathroom, :dryer, :tv, :wifi, :pet, :refrigerator, :smoking, :img)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hotel_id', $hotel_id, PDO::PARAM_INT);
        $stmt->bindValue(':room_name', $room_name, PDO::PARAM_STR);
        $stmt->bindValue(':bed_number', $bed_number, PDO::PARAM_INT);
        $stmt->bindValue(':bathroom', $bathroom, PDO::PARAM_INT);
        $stmt->bindValue(':dryer', $dryer, PDO::PARAM_INT);
        $stmt->bindValue(':tv', $tv, PDO::PARAM_INT);
        $stmt->bindValue(':wifi', $wifi, PDO::PARAM_INT);
        $stmt->bindValue(':pet', $pet, PDO::PARAM_INT);
        $stmt->bindValue(':refrigerator', $refrigerator, PDO::PARAM_INT);
        $stmt->bindValue(':smoking', $smoking, PDO::PARAM_INT);
        $stmt->bindValue(':img', $img, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: hotel_room.php');
        exit;
    }
} catch (PDOException $e) {
    echo 'データベースエラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
} catch (Exception $e) {
    echo 'エラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}

include_once __DIR__ . '/../inc/header.php';
?>

<head>
    <link rel="stylesheet" href="./css/hotel_add_room.css">
</head>

<body>
    <h1 class="title">部屋情報追加</h1>
    <div class="container">
        <form method="post" id="insert-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="room-name">部屋名:</label>
                <input type="text" id="room-name" name="room_name" required>
            </div>
            <div class="form-group">
                <label for="bed-number">ベッド数:</label>
                <input type="number" id="bed-number" name="bed_number" required>
            </div>
            <div class="form-group">
                <label>バスルーム:</label>
                <label><input type="radio" name="bathroom" value="1" required> あり</label>
                <label><input type="radio" name="bathroom" value="0" required> なし</label>
            </div>
            <div class="form-group">
                <label>ドライヤー:</label>
                <label><input type="radio" name="dryer" value="1" required> あり</label>
                <label><input type="radio" name="dryer" value="0" required> なし</label>
            </div>
            <div class="form-group">
                <label>テレビ:</label>
                <label><input type="radio" name="tv" value="1" required> あり</label>
                <label><input type="radio" name="tv" value="0" required> なし</label>
            </div>
            <div class="form-group">
                <label>Wi-Fi:</label>
                <label><input type="radio" name="wifi" value="1" required> あり</label>
                <label><input type="radio" name="wifi" value="0" required> なし</label>
            </div>
            <div class="form-group">
                <label>ペット:</label>
                <label><input type="radio" name="pet" value="1" required> 可</label>
                <label><input type="radio" name="pet" value="0" required> 不可</label>
            </div>
            <div class="form-group">
                <label>冷蔵庫:</label>
                <label><input type="radio" name="refrigerator" value="1" required> あり</label>
                <label><input type="radio" name="refrigerator" value="0" required> なし</label>
            </div>
            <div class="form-group">
                <label>喫煙:</label>
                <label><input type="radio" name="smoking" value="1" required> 可</label>
                <label><input type="radio" name="smoking" value="0" required> 不可</label>
            </div>
            <div class="form-group">
                <label for="room-photo">部屋写真:</label>
                <input type="file" id="room-photo" name="room-photo" accept="image/*">
            </div>
            <div class="buttons">
                <button type="submit">追加</button>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('insert-form').addEventListener('submit', function(e) {
            if (!confirm('この内容で登録しますか？')) {
                e.preventDefault();
            }
        });
    </script>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>