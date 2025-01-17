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
        $_SESSION["email"] = "k022@2";
        $email = $_SESSION["email"];
        $hotel_name = $_POST['hotel_name'];
        $postal_code = $_POST['postal_code'];
        $address = $_POST['address'];
        $building_name = $_POST['building_name'];
        $phone_number = $_POST['phone_number'];
        $email2 = $_POST['email'];
        $description = $_POST['hotel_explain'];
        $img_name = $_FILES['hotel_photo']['name'];

        // Check if a new file is uploaded
        if (!empty($img_name)) {
            $img_name = "hotel/" . uniqid(mt_rand(), true) . '.' . pathinfo($img_name, PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['hotel_photo']['tmp_name'], __DIR__ . '/../uploads/' . $img_name);
        } else {
            // Keep the old image if no new image is uploaded
            $img_name = $_POST['current_image'];
        }

        $sql = "UPDATE HOTEL SET
                    HOTEL_NAME = :hotel_name,
                    ZIP = :postal_code,
                    ADDRESS = :address,
                    BUILDING_NAME = :building_name,
                    PHONE_NUMBER = :phone_number,
                    EMAIL = :email2,
                    HOTEL_EXPLAIN = :description,
                    HOTEL_IMAGE = :img_name
                WHERE EMAIL = :email";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hotel_name', $hotel_name, PDO::PARAM_STR);
        $stmt->bindValue(':postal_code', $postal_code, PDO::PARAM_STR);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->bindValue(':building_name', $building_name, PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $phone_number, PDO::PARAM_STR);
        $stmt->bindValue(':email2', $email2, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':img_name', $img_name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        $_SESSION["email"] = $email2;
        header('Location: hotel_main.php');
    } catch (PDOException $e) {
        echo 'エラー: ' . $e->getMessage();
    }
} else {
    try {
        $_SESSION["email"] = "k022@2";
        $email = $_SESSION["email"];
        $sql = 'SELECT * FROM HOTEL WHERE email = :email';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $hotel = $stmt->fetch();
        if (!$hotel) {
            header('Location: hotel_list.php');
            exit;
        }
        $hotel_name = $hotel['HOTEL_NAME'];
        $postal_code = $hotel['ZIP'];
        $address = $hotel['ADDRESS'];
        $building_name = $hotel['BUILDING_NAME'];
        $phone_number = $hotel['PHONE_NUMBER'];
        $email2 = $hotel['EMAIL'];
        $description = $hotel['HOTEL_EXPLAIN'];
        $photo = $hotel['HOTEL_IMAGE'];

    } catch (PDOException $e) {
        echo ''. $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録情報更新</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #e6f7ff; color: #333; }
        .container { margin-top: 60px; padding: 20px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .form-group { display: flex; align-items: center; margin-bottom: 15px; }
        .form-group label { width: 40%; text-align: left; color: #555; padding-right: 20px; }
        .form-group input, .form-group textarea { flex: 1; max-width: 60%; padding: 10px; box-sizing: border-box; border: 1px solid #d1e9ff; border-radius: 5px; background-color: #f6fbff; color: #333; }
        .form-group textarea { resize: none; }
        .form-group input:focus { outline: none; border-color: #80c8ff; background-color: #eaf5ff; }
        .buttons { text-align: center; margin-top: 20px; }
        .buttons button { padding: 10px 20px; font-size: 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }
        .buttons button:hover { background-color: #45a049; }
        .back-button { position: absolute; top: 20px; left: 20px; padding: 10px 20px; font-size: 16px; background-color: #d1e9ff; border: 1px solid #80c8ff; border-radius: 5px; color: #333; cursor: pointer; transition: background-color 0.3s; }
        .back-button:hover { background-color: #80c8ff; }
        .title { text-align: center; margin-bottom: 30px; color: #333; }
        .popup { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 10px; z-index: 1000; }
        .popup button { padding: 10px 20px; font-size: 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px; }
        .popup button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <button onclick="history.back()" class="back-button">戻る</button>
    <h1 class="title">登録情報更新</h1>
    <div class="container">
        <form onsubmit="return Check()" id="update-form" enctype="multipart/form-data" method="post">
            <div class="form-group">
                <label for="postal-code">郵便番号: 123-4567</label>
                <input type="text" id="postal-code" name="postal_code" value="<?php echo htmlspecialchars($postal_code, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="address">住所: 東京都新宿区西新宿1-1-1</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="building">建物名: サンプルビル</label>
                <input type="text" id="building" name="building_name" value="<?php echo htmlspecialchars($building_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="phone">電話番号: 090-1234-5678</label>
                <input type="tel" id="phone" name="phone_number" value="<?php echo htmlspecialchars($phone_number, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="email">メール: yamada@example.com</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email2, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="hotel-name">ホテル名:</label>
                <input type="text" id="hotel-name" name="hotel_name" value="<?php echo htmlspecialchars($hotel_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="hotel-photo">ホテルの写真:</label>
                <input type="file" id="hotel-photo" name="hotel_photo" accept="image/*">
                <img src="<?php echo '../uploads/' . htmlspecialchars($photo, ENT_QUOTES, 'UTF-8'); ?>" alt="hotel-photo" width="200" height="200">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($photo, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="hotel-explain">ホテルの説明:</label>
                <textarea id="hotel-explain" name="hotel_explain" rows="5"><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="buttons">
                <button type="submit">更新</button>
            </div>
        </form>
    </div>
    <script>
    function Check() {
        event.preventDefault();
        var form = document.getElementById('update-form');
        const postalCode = document.getElementById('postal-code').value;
        const building = document.getElementById('building').value;
        const address = document.getElementById('address').value;
        const phone = document.getElementById('phone').value;
        const email = document.getElementById('email').value;
        const hotelName = document.getElementById('hotel-name').value;
        const hotelPhoto = document.getElementById('hotel-photo').files[0];
        const hotelExplain = document.getElementById('hotel-explain').value;

        const popup = document.createElement('div');
        popup.className = 'popup';
        popup.innerHTML = `
            <p>更新しますか？</p>
            <p>郵便番号: ${postalCode}</p>
            <p>住所: ${address}</p>
            <p>建物名: ${building}</p>
            <p>電話番号: ${phone}</p>
            <p>メール: ${email}</p>
            <p>ホテル名: ${hotelName}</p>
            <p>ホテルの写真: ${hotelPhoto ? hotelPhoto.name : '選択されていません'}</p>
            <p>ホテルの説明: ${hotelExplain}</p>
            <button id="confirm-yes">はい</button>
            <button id="confirm-no">いいえ</button>
        `;
        document.body.appendChild(popup);

        document.getElementById('confirm-yes').addEventListener('click', function() {
            form.submit();
        });

        document.getElementById('confirm-no').addEventListener('click', function() {
            document.body.removeChild(popup);
        });
    }
    </script>
</body>
</html>