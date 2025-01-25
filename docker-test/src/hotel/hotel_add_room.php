<?php
session_start();
ob_start();
include_once(__DIR__ . '/../inc/db.php');
$db = db_connect();
if ($db === false) {
    header('Location: error.php');
    exit;
}

if ($_POST) {
    try {
        $room_id = $_POST['room_id'];
        $hotel_id = $_POST['hotel_id'];
        $room_name = $_POST['room_name'];
        $bed_number = $_POST['bed_number'];
        $bathroom = $_POST['bathroom'];
        $dryer = $_POST['dryer'];
        $tv = $_POST['tv'];
        $wifi = $_POST['wifi'];
        $pet = $_POST['pet'];
        $refrigerator = $_POST['refrigerator'];
        $smoking = $_POST['smoking'];

        $sql = "INSERT INTO ROOM (ROOM_ID, HOTEL_ID, ROOM_NAME, BED_NUMBER, BATHROOM, DRYER, TV, WI_FI, PET, REFRIGERATOR, SMOKING) VALUES
                (:room_id, :hotel_id, :room_name, :bed_number, :bathroom, :dryer, :tv, :wifi, :pet, :refrigerator, :smoking)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
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

        $stmt->execute();

        header('Location: room_list.php');
    } catch (PDOException $e) {
        echo 'エラー: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>部屋情報追加</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #e6f7ff; color: #333; }
        .container { margin-top: 60px; padding: 20px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .form-group { display: flex; align-items: center; margin-bottom: 15px; }
        .form-group label { width: 40%; text-align: left; color: #555; padding-right: 20px; }
        .form-group input, .form-group textarea { flex: 1; max-width: 60%; padding: 10px; box-sizing: border-box; border: 1px solid #d1e9ff; border-radius: 5px; background-color: #f6fbff; color: #333; }
        .form-group input:focus { outline: none; border-color: #80c8ff; background-color: #eaf5ff; }
        .buttons { text-align: center; margin-top: 20px; }
        .buttons button { padding: 10px 20px; font-size: 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }
        .buttons button:hover { background-color: #45a049; }
        .back-button { position: absolute; top: 20px; left: 20px; padding: 10px 20px; font-size: 16px; background-color: #d1e9ff; border: 1px solid #80c8ff; border-radius: 5px; color: #333; cursor: pointer; transition: background-color 0.3s; }
        .back-button:hover { background-color: #80c8ff; }
        .title { text-align: center; margin-bottom: 30px; color: #333; }
    </style>
</head>
<body>
    <button onclick="history.back()" class="back-button">戻る</button>
    <h1 class="title">部屋情報追加</h1>
    <div class="container">
        <form method="post" id="insert-form">
            <div class="form-group">
                <label for="room-id">部屋ID:</label>
                <input type="number" id="room-id" name="room_id" required>
            </div>
            <div class="form-group">
                <label for="hotel-id">ホテルID:</label>
                <input type="number" id="hotel-id" name="hotel_id" required>
            </div>
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
            <div class="buttons">
                <button type="submit">追加</button>
            </div>
        </form>
    </div>
</body>
</html>
