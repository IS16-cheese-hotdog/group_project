<?php
include_once(__DIR__ . '/../inc/is_login.php');
include_once(__DIR__ . '/../inc/get_url.php');
include_once(__DIR__ . '/../inc/db.php');

ob_start();
$url = get_url();
$db = db_connect();

if ($db === false) {
    header('Location: ' . $url . '/err.php?err_msg=DB接続エラーです');
    exit;
}

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 入力値の取得とバリデーション
        $hotel_name = trim($_POST['hotel_name'] ?? '');
        $postal_code = trim($_POST['postal_code'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $building_name = trim($_POST['building_name'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $email2 = trim($_POST['email'] ?? '');
        $description = trim($_POST['hotel_explain'] ?? '');
        $img_name = $_FILES['hotel_photo']['name'] ?? '';

        // ファイルアップロード処理
        if (!empty($img_name)) {
            $img_name = uniqid(mt_rand(), true) . '.' . pathinfo($img_name, PATHINFO_EXTENSION);
            $upload_path = __DIR__ . '/../uploads/hotel/' . $img_name;

            if (move_uploaded_file($_FILES['hotel_photo']['tmp_name'], $upload_path) === false) {
                throw new Exception('画像のアップロードに失敗しました');
            }
        } else {
            $img_name = $_POST['current_image'] ?? '';
        }

        // SQL更新クエリ
        $sql = "UPDATE HOTEL SET
                    HOTEL_NAME = :hotel_name,
                    ZIP = :postal_code,
                    ADDRESS = :address,
                    BUILDING_NAME = :building_name,
                    PHONE_NUMBER = :phone_number,
                    EMAIL = :email2,
                    HOTEL_EXPLAIN = :description,
                    HOTEL_IMAGE = :img_name
                WHERE HOTEL_ID = :hotel_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hotel_name', $hotel_name, PDO::PARAM_STR);
        $stmt->bindValue(':postal_code', $postal_code, PDO::PARAM_STR);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->bindValue(':building_name', $building_name, PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $phone_number, PDO::PARAM_STR);
        $stmt->bindValue(':email2', $email2, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':img_name', $img_name, PDO::PARAM_STR);
        $stmt->bindValue(':hotel_id', $_SESSION['hotel_id'], PDO::PARAM_STR);

        $stmt->execute();

        header('Location: hotel_main.php');
        exit;
    } catch (Exception $e) {
        $error_message = 'エラー: ' . $e->getMessage();
    }
} else {
    // GETリクエストでホテル情報を取得
    try {
        $sql = 'SELECT * FROM HOTEL WHERE HOTEL_ID = :hotel_id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hotel_id', $_SESSION['hotel_id'], PDO::PARAM_STR);
        $stmt->execute();
        $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$hotel) {
            header('Location: hotel_.php');
            exit;
        }

        // 変数に値を格納
        $hotel_name = $hotel['HOTEL_NAME'];
        $postal_code = $hotel['ZIP'];
        $address = $hotel['ADDRESS'];
        $building_name = $hotel['BUILDING_NAME'];
        $phone_number = $hotel['PHONE_NUMBER'];
        $email2 = $hotel['EMAIL'];
        $description = $hotel['HOTEL_EXPLAIN'];
        $photo = $hotel['HOTEL_IMAGE'];
    } catch (Exception $e) {
        $error_message = 'エラー: ' . $e->getMessage();
    }
}
?>
<?php include_once(__DIR__ . '/../inc/header.php'); ?>
    <link rel="stylesheet" href="./css/hotel_update.css">
</head>
<body>
    <h1 class="title">登録情報更新</h1>

    <?php if (!empty($error_message)): ?>
        <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <div class="container">
        <form enctype="multipart/form-data" method="post">
            <div class="form-group">
                <label for="postal-code">郵便番号:</label>
                <input type="text" id="postal-code" name="postal_code" value="<?php echo htmlspecialchars($postal_code, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="address">住所:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="building">建物名:</label>
                <input type="text" id="building" name="building_name" value="<?php echo htmlspecialchars($building_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="phone">電話番号:</label>
                <input type="tel" id="phone" name="phone_number" value="<?php echo htmlspecialchars($phone_number, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="email">メール:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email2, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="hotel-name">ホテル名:</label>
                <input type="text" id="hotel-name" name="hotel_name" value="<?php echo htmlspecialchars($hotel_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label for="hotel-photo">ホテルの写真:</label>
                <input type="file" id="hotel-photo" name="hotel_photo" accept="image/*">
                <?php if (!empty($photo)): ?>
                    <img src="../uploads/hotel/<?php echo htmlspecialchars($photo, ENT_QUOTES, 'UTF-8'); ?>" alt="hotel-photo" width="200">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($photo, ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="hotel-explain">ホテルの説明:</label>
                <textarea id="hotel-explain" name="hotel_explain" rows="5"><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="update-button">
                <button type="submit">更新</button>
            </div>
        </form>
    </div>
<?php ob_end_flush(); ?>
<?php include_once(__DIR__ . '/../inc/footer.php'); ?>