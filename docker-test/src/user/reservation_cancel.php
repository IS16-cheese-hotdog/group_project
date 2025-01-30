<?php
include_once __DIR__ . '/../inc/is_login.php';
include_once __DIR__ . '/../inc/db.php';

$pdo = db_connect();
try {
    $pdo->beginTransaction();
    $sql = 'DELETE FROM RESERVATION WHERE RESERVATION_ID = :reservation_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':reservation_id', $_POST['reservation_id'], PDO::PARAM_INT);
    if ($stmt->execute()) {
        $pdo->commit();
        header('Location: user_check.php');
        exit;
    } else {
        echo "<script>alert('予約のキャンセルに失敗しました');window.location.href='user_check.php';</script>";
    }
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "<script>alert('予約のキャンセルに失敗しました');window.location.href='user_check.php';</script>";
    exit;
}
?>
