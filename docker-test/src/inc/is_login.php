<?php
include_once __DIR__ . '/get_url.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // 未ログインの場合
    $url = get_url();
    header('Location: ' . $url . '/login.html');
    exit;
}
?>