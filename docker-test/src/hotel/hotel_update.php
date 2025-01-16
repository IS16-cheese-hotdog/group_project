<?php
//include_once(__DIR__ . '/../inc/is_login.php');
include_once(__DIR__ . '/../inc/db.php');
$db = db_connect();
if ($db === false) {
    // DB接続エラーの場合
    header('Location: error.php');
    exit;
}

if ($_POST) {
    $hotel_name = $_POST['hotel_name'];
    $postal_code = $_POST['postal_code'];
    $address = $_POST['address'];
    $building_name = $_POST['building_name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $description = $_POST['description'];
    $photo = $_POST['photo'];
    $img_name = $_FILES['hotel-photo']['name'];

    $img_name = "hotel/" . uniqid(mt_rand(), true) . substr(strrchr($img_name, '.'), 1);
    move_uploaded_file($_FILES['hotel-photo']['tmp_name'], __DIR__ . '/../uploads/' . $img_name);

    $sql = 'UPDATE HOTEL SET hotel_name = :hotel_name, postal_code = :postal_code, address = :address, building_name = :building_name, phone_number = :phone_number, email = :email, description = :description, photo = :photo WHERE hotel_id = :hotel_id';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':hotel_name', $hotel_name, PDO::PARAM_STR);
    $stmt->bindValue(':postal_code', $postal_code, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address, PDO::PARAM_STR);
    $stmt->bindValue(':building_name', $building_name, PDO::PARAM_STR);
    $stmt->bindValue(':phone_number', $phone_number, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':photo', $img_name, PDO::PARAM_STR);
    $stmt->execute();

} else {

}



?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録情報更新</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #e6f7ff;
            color: #333;
        }
        .container {
            margin-top: 60px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-group label {
            width: 40%;
            text-align: left;
            color: #555;
            padding-right: 20px;
        }
        .form-group input {
            flex: 1;
            max-width: 60%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #d1e9ff;
            border-radius: 5px;
            background-color: #f6fbff;
            color: #333;
        }
        .form-group textarea {
            flex: 1;
            max-width: 60%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #d1e9ff;
            border-radius: 5px;
            background-color: #f6fbff;
            color: #333;
            resize: none;
        }
        .form-group input:focus {
            outline: none;
            border-color: #80c8ff;
            background-color: #eaf5ff;
        }
        .buttons {
            text-align: center;
            margin-top: 20px;
        }
        .buttons button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .buttons button:hover {
            background-color: #45a049;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #d1e9ff;
            border: 1px solid #80c8ff;
            border-radius: 5px;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #80c8ff;
        }
        .title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 1000;
        }
        .popup button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        .popup button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <button onclick="history.back()" class="back-button">戻る</button>
    <h1 class="title">登録情報更新</h1>
    <div class="container">
        <form onsubmit="return Check()" id="update-form" enctype="multipart/form-data" method="post">
            <div class="form-group">
                <label for="postal-code">郵便番号: 123-4567</label>
                <input type="text" id="postal-code" name="postal-code" value="123-4567">
            </div>
            <div class="form-group">
                <label for="address">住所: 東京都新宿区西新宿1-1-1</label>
                <input type="text" id="address" name="address" value="東京都新宿区西新宿1-1-1">
            </div>
            <div class="form-group">
                <label for="building">建物名: サンプルビル</label>
                <input type="text" id="building" name="building" value="サンプルビル">
            </div>
            <div class="form-group">
                <label for="phone">電話番号: 090-1234-5678</label>
                <input type="tel" id="phone" name="phone" value="090-1234-5678">
            </div>
            <div class="form-group">
                <label for="email">メール: yamada@example.com</label>
                <input type="email" id="email" name="email" value="yamada@example.com">
            </div>
            <div class="form-group">
                <label for="hotel-name">ホテル名:</label>
                <input type="text" id="hotel-name" name="hotel-name">
            </div>
            <div class="form-group">
                <label for="hotel-photo">ホテルの写真:</label>
                <input type="file" id="hotel-photo" name="hotel-photo" accept="image/*">
            </div>
            <div class="form-group">
                <label for="hotel-explain">ホテルの説明:</label>
                <textarea id="hotel-explain" name="hotel-explain" rows="5" style="width: 60%;"></textarea>
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
        };
    ;
    </script>
</body>
</html>
