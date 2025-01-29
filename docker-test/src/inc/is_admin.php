<?php
session_start();
if(!isset($_SESSION["admin_id"])) {
    include_once("get_url.php");
    $url = get_url();
    header('Location: ' . $url . '/login.html');
    exit;
}
?>