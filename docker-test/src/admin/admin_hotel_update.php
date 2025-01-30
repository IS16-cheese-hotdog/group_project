<?php
include_once __DIR__ . '/../inc/is_admin.php';
include_once __DIR__ . '/../inc/db.php';
$db = db_connect();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_hotel_id'])) {
    try {
        $db->beginTransaction();
        
        $update_fields = [];
        $params = [":hotel_id" => $_POST["update_hotel_id"]];

        if (!empty($_POST["update_hotel_name"])) {
            $update_fields[] = "HOTEL_NAME = :hotel_name";
            $params[":hotel_name"] = $_POST["update_hotel_name"];
        }
        if (!empty($_POST["update_hotel_address"])) {
            $update_fields[] = "ADDRESS = :hotel_address";
            $params[":hotel_address"] = $_POST["update_hotel_address"];
        }
        if (!empty($_POST["update_hotel_tel"])) {
            $update_fields[] = "PHONE_NUMBER = :hotel_tel";
            $params[":hotel_tel"] = $_POST["update_hotel_tel"];
        }
        if (!empty($_POST["update_hotel_image"])) {
            $update_fields[] = "HOTEL_IMAGE = :hotel_image";
            $params[":hotel_image"] = $_POST["update_hotel_image"];
        }
        if (!empty($_POST["update_hotel_detail"])) {
            $update_fields[] = "HOTEL_EXPLAIN = :hotel_detail";
            $params[":hotel_detail"] = $_POST["update_hotel_detail"];
        }
        if (!empty($_POST["update_hotel_password"])) {
            $update_fields[] = "USER_PASSWORD = :hotel_password";
            $params[":hotel_password"] = password_hash($_POST["update_hotel_password"], PASSWORD_DEFAULT);
        }

        if (!empty($update_fields)) {
            $sql = "UPDATE HOTEL SET " . implode(", ", $update_fields) . " WHERE HOTEL_ID = :hotel_id";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
        }

        if (!empty($_POST["update_hotel_email"])) {
            $stmt = $db->prepare("UPDATE EMAIL SET EMAIL = :hotel_email WHERE HOTEL_ID = :hotel_id");
            $stmt->bindParam(":hotel_id", $_POST["update_hotel_id"]);
            $stmt->bindParam(":hotel_email", $_POST["update_hotel_email"]);
            $stmt->execute();
        }

        $db->commit();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $db->rollback();
        echo "エラー：" . $e->getMessage();
    }
} else {
    $hotel_id = $_POST["hotel_id"];
    $stmt = $db->prepare("SELECT * FROM HOTEL LEFT JOIN USER ON USER.ROLE = HOTEL.HOTEL_ID LEFT JOIN EMAIL ON EMAIL.USER_ID = USER.USER_ID WHERE HOTEL.HOTEL_ID = :hotel_id");
    $stmt->bindParam(":hotel_id", $hotel_id);
    $stmt->execute();
    $hotel = $stmt->fetch();
}
?>

<?php include_once __DIR__ . '/../inc/header.php'; ?>

<h1>ホテル情報変更</h1>
<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="update_hotel_id" value="<?php echo $hotel['HOTEL_ID']; ?>">
    <div>
        <label for="hotel_name">ホテル名</label>
        <input type="text" name="update_hotel_name" id="hotel_name" value="<?php echo $hotel['HOTEL_NAME']; ?>">
    </div>
    <div>
        <label for="hotel_address">住所</label>
        <input type="text" name="update_hotel_address" id="hotel_address" value="<?php echo $hotel['ADDRESS']; ?>">
    </div>
    <div>
        <label for="hotel_tel">電話番号</label>
        <input type="text" name="update_hotel_tel" id="hotel_tel" value="<?php echo $hotel['PHONE_NUMBER']; ?>">
    </div>
    <div>
        <label for="hotel_email">メールアドレス</label>
        <input type="email" name="update_hotel_email" id="hotel_email" value="<?php echo $hotel['EMAIL']; ?>">
    </div>
    <div>
        <label for="hotel_image">画像</label>
        <input type="file" name="update_hotel_image" id="hotel_image">
    </div>
    <div>
        <label for="hotel_detail">ホテル説明</label>
        <textarea name="update_hotel_detail" id="hotel_detail"><?php echo $hotel['HOTEL_EXPLAIN']; ?></textarea>
    </div>
    <div>
        <label for="hotel_password">パスワード</label>
        <input type="password" name="update_hotel_password" id="hotel_password">
    </div>
    <button type="submit">変更</button>
</form>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>
