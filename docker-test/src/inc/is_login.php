<?php
include_once __DIR__ . '/get_url.php';
<<<<<<< HEAD
if (!isset($_SESSION['email'])) {
    // 未ログインの場合
    $url = get_url();
    header('Location: ' . $url . '/login.php');

=======
session_start();

if (!isset($_SESSION['user_id'])) {
    // 未ログインの場合
    $url = get_url();
    header('Location: ' . $url . '/login.html');
>>>>>>> d02ab1f9dcea0fec5f2e9510ee6e1bed590b90be
    exit;
}
?>