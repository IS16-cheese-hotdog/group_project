<?php
include_once(__DIR__ . '/../inc/is_hotel.php');
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
        $prefecture = trim($_POST['prefecture'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $building_name = trim($_POST['building_name'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $email2 = trim($_POST['email'] ?? '');
        $description = trim($_POST['hotel_explain'] ?? '');
        $img_name = $_FILES['hotel_photo']['name'] ?? '';

        // 必須項目のバリデーション
        if (empty($hotel_name) || empty($postal_code) || empty($prefecture) || empty($address) || empty($phone_number) || empty($email2)) {
            throw new Exception('必須項目をすべて入力してください。');
        }

        // メールアドレスの形式チェック
        if (!filter_var($email2, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('正しいメールアドレスを入力してください。');
        }

        // 郵便番号の形式チェック（日本の郵便番号フォーマット 例: 123-4567）
        if (!preg_match('/^\d{7}$/', $postal_code)) {
            throw new Exception('郵便番号の形式が正しくありません (例: 1234567)。');
        }

        // 電話番号の形式チェック（例: 090-1234-5678 または 03-1234-5678）
        if (!preg_match('/^0\d{1,4}-\d{1,4}-\d{4}$/', $phone_number)) {
            throw new Exception('電話番号の形式が正しくありません (例: 090-1234-5678)。');
        }

        // ファイルアップロード処理
        if (!empty($img_name)) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_extension = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
            $mime_type = mime_content_type($_FILES['hotel_photo']['tmp_name']);

            if (!in_array($file_extension, $allowed_extensions) || strpos($mime_type, 'image') === false) {
                throw new Exception('画像ファイル（JPG, JPEG, PNG, GIF）のみアップロード可能です。');
            }

            $img_name = uniqid(mt_rand(), true) . '.' . $file_extension;
            $upload_path = __DIR__ . '/../uploads/hotel/' . $img_name;

            if (!move_uploaded_file($_FILES['hotel_photo']['tmp_name'], $upload_path)) {
                throw new Exception('画像のアップロードに失敗しました。');
            }
        } else {
            $img_name = $_POST['current_image'] ?? '';
        }
        $db->beginTransaction();
        // SQL更新クエリ
        $sql = "UPDATE HOTEL SET
                    HOTEL.HOTEL_NAME = :hotel_name,
                    HOTEL.ZIP = :postal_code,
                    HOTEL.PREFECTURE = :prefecture,
                    HOTEL.ADDRESS = :address,
                    HOTEL.BUILDING_NAME = :building_name,
                    HOTEL.PHONE_NUMBER = :phone_number,
                    HOTEL.HOTEL_EXPLAIN = :description,
                    HOTEL.HOTEL_IMAGE = :img_name
                WHERE HOTEL.HOTEL_ID = :hotel_id";


        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hotel_name', $hotel_name, PDO::PARAM_STR);
        $stmt->bindValue(':postal_code', $postal_code, PDO::PARAM_STR);
        $stmt->bindValue(':prefecture', $prefecture, PDO::PARAM_STR);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->bindValue(':building_name', $building_name, PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $phone_number, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':img_name', $img_name, PDO::PARAM_STR);
        $stmt->bindValue(':hotel_id', $_SESSION['hotel_id'], PDO::PARAM_INT);
        $stmt->execute();

        $sql = "UPDATE EMAIL SET EMAIL = :email WHERE USER_ID = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
        $stmt->bindValue(":email", $email2, PDO::PARAM_STR);
        $stmt->execute();

        $db->commit();

        header('Location: hotel_main.php');
        exit;
    } catch (Exception $e) {
        $error_message = 'エラー: ' . $e->getMessage();
    }
} else {
    // GETリクエストでホテル情報を取得
    try {
        $sql = 'SELECT USER.USER_ID, HOTEL.*, EMAIL.EMAIL FROM HOTEL LEFT JOIN USER ON USER.ROLE = HOTEL.HOTEL_ID LEFT JOIN EMAIL ON EMAIL.USER_ID = USER.USER_ID WHERE HOTEL.HOTEL_ID = :hotel_id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hotel_id', $_SESSION['hotel_id'], PDO::PARAM_INT);
        $stmt->execute();
        $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$hotel) {
            header('Location: hotel_main.php');
            exit;
        }

        // 変数に値を格納
        $hotel_name = $hotel['HOTEL_NAME'];
        $postal_code = $hotel['ZIP'];
        $prefecture = $hotel['PREFECTURE'];
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
                <label for="prefecture">都道府県:</label>
                <select id="prefecture" name="prefecture">
                    <?php
                    $prefectures = [
                        "北海道", "青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県",
                        "茨城県", "栃木県", "群馬県", "埼玉県", "千葉県", "東京都", "神奈川県",
                        "新潟県", "富山県", "石川県", "福井県", "山梨県", "長野県", "岐阜県",
                        "静岡県", "愛知県", "三重県", "滋賀県", "京都府", "大阪府", "兵庫県",
                        "奈良県", "和歌山県", "鳥取県", "島根県", "岡山県", "広島県", "山口県",
                        "徳島県", "香川県", "愛媛県", "高知県", "福岡県", "佐賀県", "長崎県",
                        "熊本県", "大分県", "宮崎県", "鹿児島県", "沖縄県"
                    ];

                    foreach ($prefectures as $pref) {
                        $selected = ($pref === $prefecture) ? 'selected' : '';
                        echo "<option value=\"$pref\" $selected>$pref</option>";
                    }
            ?>
        </select>
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