<?php
include_once __DIR__ . '/../inc/is_admin.php';
include_once __DIR__ . '/../inc/db.php';
$db = db_connect();

$search_email = isset($_GET['email']) ? trim($_GET['email']) : '';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (!empty($search_email)) {
        $stmt = $db->prepare('SELECT USER.*, EMAIL.EMAIL FROM USER JOIN EMAIL ON USER.USER_ID = EMAIL.USER_ID WHERE EMAIL.EMAIL LIKE :email');
        $stmt->bindValue(':email', "%$search_email%", PDO::PARAM_STR);
        $stmt->execute();
    } else {
        $stmt = $db->query('SELECT USER.*, EMAIL.EMAIL FROM USER JOIN EMAIL ON USER.USER_ID = EMAIL.USER_ID');
    }
    $users = $stmt->fetchAll();
} elseif (isset($_POST['delete_user_id'])) {
    try {
        $db->beginTransaction();
        $user_id = $_POST['delete_user_id'];
        
        $stmt = $db->prepare('DELETE FROM EMAIL WHERE USER_ID = :user_id');
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        
        $stmt = $db->prepare('DELETE FROM USER WHERE USER_ID = :user_id');
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        
        $db->commit();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $db->rollBack();
        $error_message = 'エラー: ' . $e->getMessage();
    }
}
?>

<?php include_once __DIR__ . '/../inc/header.php';?>
    <h1>ユーザー情報</h1>
    <main>
        <form method="GET" action="">
            <label for="email">メールアドレスを入力してください:</label>
            <input type="text" name="email" id="email" value="<?= htmlspecialchars($search_email) ?>">
            <button type="submit">検索</button>
        </form>

        <table>
            <tr>
                <th>ユーザーID</th>
                <th>ユーザー名</th>
                <th>メールアドレス</th>
                <th>住所</th>
                <th>役割</th>
                <th>性別</th>
                <th>生年月日</th>
                <th>操作</th>
                <th>削除</th>
            </tr>
            <?php if (empty($users)) : ?>
                <tr><td colspan="9" style="text-align:center; color:red;">該当するユーザーが見つかりません。</td></tr>
            <?php else : ?>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= $user['USER_ID'] ?></td>
                        <td><?= htmlspecialchars($user['USER_NAME']) ?></td>
                        <td><?= $user['EMAIL'] ?></td>
                        <td><?= htmlspecialchars($user['ADDRESS']) ?></td>
                        <td><?= htmlspecialchars($user['ROLE']) ?></td>
                        <td><?= $user['GENDER'] ?></td>
                        <td><?= $user['DATE_OF_BIRTH'] ?></td>
                        <td>
                            <form method="POST" action="admin_user_change.php">
                                <input type="hidden" name="user_id" value="<?= $user['USER_ID'] ?>">
                                <button type="submit">変更</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="delete_user_id" value="<?= $user['USER_ID'] ?>">
                                <button type="submit">削除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </main>
</body>
</html>
