<?php
include_once(__DIR__ . '/../inc/is_admin.php');
include_once(__DIR__ . '/../inc/db.php');
$db = db_connect();
if (!$db) {
    die('データベースの接続に失敗しました。');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $err = 'err: ';
    try {
        $db->beginTransaction();

        // 入力データ取得
        $zip = trim($_POST['postal_code']);
        $prefecture = trim($_POST['prefecture']);
        $address = trim($_POST['address']);
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone']);
        $hotel_name = trim($_POST['hotel_name']);
        $building_name = trim($_POST['building_name'] ?? '');
        $hotel_explain = trim($_POST['hotel_explain']);
        $passwordInput = $_POST['password'];

        // バリデーション
        if (!$zip || !$address || !$email || !$phone_number || !$hotel_name || !$passwordInput) {
            throw new Exception('必須項目をすべて入力してください。');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('正しいメールアドレスを入力してください。');
        }
        if (!preg_match('/^\d{7}$/', $zip)) {
            throw new Exception('郵便番号は7桁の数字で入力してください (例: 1234567)。');
        }
        if (!preg_match('/^0\d{1,4}-\d{1,4}-\d{4}$/', $phone_number)) {
            throw new Exception('電話番号の形式が正しくありません (例: 090-1234-5678)。');
        }

        // 画像アップロード処理
        $img_name = '';
        if (!empty($_FILES['hotel_photo']['name'])) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_extension = strtolower(pathinfo($_FILES['hotel_photo']['name'], PATHINFO_EXTENSION));
            $mime_type = mime_content_type($_FILES['hotel_photo']['tmp_name']);

            if (!in_array($file_extension, $allowed_extensions) || strpos($mime_type, 'image') === false) {
                throw new Exception('画像ファイル（JPG, JPEG, PNG, GIF）のみアップロード可能です。');
            }

            $img_name = uniqid(mt_rand(), true) . '.' . $file_extension;
            $upload_path = __DIR__ . '/../uploads/hotel/' . $img_name;

            if (!move_uploaded_file($_FILES['hotel_photo']['tmp_name'], $upload_path)) {
                throw new Exception('画像のアップロードに失敗しました。');
            }
        }
        // HOTELテーブルにデータを挿入
        $stmt = $db->prepare('INSERT INTO HOTEL (ZIP, PREFECTURE, ADDRESS, PHONE_NUMBER, HOTEL_NAME, BUILDING_NAME, HOTEL_EXPLAIN, HOTEL_IMAGE) 
        VALUES (:zip, :prefecture, :address, :phone_number, :hotel_name, :building_name, :hotel_explain, :hotel_photo)');
        $stmt->execute([
            'zip' => $zip,
            'prefecture' => $prefecture,
            'address' => $address,
            'phone_number' => $phone_number,
            'hotel_name' => $hotel_name,
            'building_name' => $building_name,
            'hotel_explain' => $hotel_explain,
            'hotel_photo' => $img_name
        ]);
        $hotel_id = $db->lastInsertId();

        // USERテーブルにデータを挿入
        $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO USER (USER_NAME, USER_PASSWORD, ADDRESS, ROLE, GENDER, DATE_OF_BIRTH) VALUES (:hotel_name, :password, :address, :hotel_id, NULL, NULL)');
        $stmt->execute([
            'hotel_name' => $hotel_name,
            'password' => $hashedPassword,
            'address' => $address,
            'hotel_id' => $hotel_id
        ]);
        $user_id = $db->lastInsertId();

        // EMAILテーブルにデータを挿入
        $stmt = $db->prepare('INSERT INTO EMAIL (EMAIL, USER_ID) VALUES (:email, :user_id)');
        $stmt->execute([
            'email' => $email,
            'user_id' => $user_id
        ]);

        $db->commit();
        echo 'ホテル情報が正常に追加されました。';
    } catch (Exception $e) {
        $db->rollBack();
        echo 'エラー: ' . $e->getMessage();
    }
}
?>


<?php include_once(__DIR__ . '/../inc/header.php'); ?>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #007BFF;
            padding: 10px 20px;
            color: #fff;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-align: center;
            flex-grow: 1;
        }

        form {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px 30px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #007BFF;
        }

        input,
        .submit-button,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #b3d8ff;
            border-radius: 6px;
            background-color: #f9fcff;
        }

        .submit-button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
    <div class="header">
        <h1>ホテル追加</h1>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="postal_code">郵便番号:</label>
        <input type="text" id="postal_code" name="postal_code" required>
        <label for="prefecture">都道府県:</label>
        <select id="prefecture" name="prefecture">
            <?php
            $prefectures = [
                "北海道",
                "青森県",
                "岩手県",
                "宮城県",
                "秋田県",
                "山形県",
                "福島県",
                "茨城県",
                "栃木県",
                "群馬県",
                "埼玉県",
                "千葉県",
                "東京都",
                "神奈川県",
                "新潟県",
                "富山県",
                "石川県",
                "福井県",
                "山梨県",
                "長野県",
                "岐阜県",
                "静岡県",
                "愛知県",
                "三重県",
                "滋賀県",
                "京都府",
                "大阪府",
                "兵庫県",
                "奈良県",
                "和歌山県",
                "鳥取県",
                "島根県",
                "岡山県",
                "広島県",
                "山口県",
                "徳島県",
                "香川県",
                "愛媛県",
                "高知県",
                "福岡県",
                "佐賀県",
                "長崎県",
                "熊本県",
                "大分県",
                "宮崎県",
                "鹿児島県",
                "沖縄県"
            ];

            foreach ($prefectures as $pref) {
                $selected = ($pref === $prefecture) ? 'selected' : '';
                echo "<option value=\"$pref\" $selected>$pref</option>";
            }
            ?>
        </select>

        <label for="address">住所:</label>
        <input type="text" id="address" name="address" required>

        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">電話番号:</label>
        <input type="tel" id="phone" name="phone" required>

        <label for="hotel_name">ホテル名:</label>
        <input type="text" id="hotel_name" name="hotel_name" required>

        <label for="building_name">建物名:</label>
        <input type="text" id="building_name" name="building_name">

        <label for="hotel_explain">ホテル説明:</label>
        <textarea id="hotel_explain" name="hotel_explain" required></textarea>

        <label for="hotel_image">ホテル画像:</label>
        <input type="file" id="hotel-photo" name="hotel_photo" accept="image/*" required>

        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit" class="submit-button">送信</button>
    </form>
<?php include_once(__DIR__ . '/../inc/footer.php'); ?>