<?php
session_start();

// ログアウト処理
$_SESSION = array();
session_destroy();
header('Location: poka.php');
exit;
?>