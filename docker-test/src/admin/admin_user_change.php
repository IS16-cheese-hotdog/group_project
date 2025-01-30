<?php
include_once __DIR__ . '/../inc/is_admin.php';
include_once __DIR__ . '/../inc/db.php';
$db = db_connect();

if (isset($_POST['user_id'])) {
    $stmt = $db->prepare('SELECT USER.*, EMAIL.EMAIL FROM USER LEFT JOIN EMAIL ON EMAIL.USER_ID = USER.USER_ID WHERE USER.USER_ID = :id');
    $stmt->bindParam(':id', $_POST["user_id"]);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $user = $stmt->fetch();
} else if (isset($_POST['change_user_id'])) {
    try {
        $db->beginTransaction();
        $stmt = $db->prepare('UPDATE USER SET USER_NAME = :name, ADDRESS = :address, DATE_OF_BIRTH = :birthdate, ROLE = :role WHERE USER_ID = :id');
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':birthdate', $_POST['birthdate']);
        $stmt->bindParam(':role', $_POST['role']);
        $stmt->bindParam(':id', $_POST['change_user_id']);
        $stmt->execute();

        $stmt = $db->prepare('UPDATE EMAIL SET EMAIL = :email WHERE USER_ID = :id');
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':id', $_POST['change_user_id']);
        $stmt->execute();

        $db->commit();
        header('Location: admin_user_manager.php');
        exit;
    } catch (Exception $e) {
        $db->rollback();
        echo 'エラー: ' . $e->getMessage();
    }
} else {
    header('Location: admin_user_manager.php');
    exit;
}

?>

<?php include_once __DIR__ . '/../inc/header.php'; ?>

<h1>ユーザー情報変更</h1>
<form action="" method="post">
    <input type="hidden" name="change_user_id" value="<?php echo $user['USER_ID']; ?>">
    <div>
        <label for="name">名前</label>
        <input type="text" name="name" id="name" value="<?php echo $user['USER_NAME']; ?>">
    </div>
    <div>
        <label for="address">住所</label>
        <input type="text" name="address" id="address" value="<?php echo $user['ADDRESS']; ?>">
    </div>
    <div>
        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="<?php echo $user['EMAIL']; ?>">
    </div>
    <div>
        <label for="birthdate">生年月日</label>
        <input type="date" name="birthdate" id="birthdate" value="<?php echo $user['DATE_OF_BIRTH']; ?>">
    </div>

    <div>
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password">
    </div>
    <div>
        <label for="role">権限</label>
        <input type="text" name="role" id="role" value="<?php echo $user['ROLE']; ?>">
    </div>
    <button type="submit">変更</button>
</form>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>