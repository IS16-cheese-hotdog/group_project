<?php
include_once __DIR__ . '/get_url.php';
if (isset($_SESSION['user_id'])) {
    // ログイン済みの場合
    session_start();
} else {
    // 未ログインの場合
    header('Location: login.php');
    exit;
}
?>