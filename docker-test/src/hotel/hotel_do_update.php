<?php
include_once(__DIR__ . '/../inc/is_login.php');
include_once(__DIR__ . '/../inc/db.php');
$db = db_connect();
if ($db === false) {
    // DB接続エラーの場合
    header('Location: error.php');
    exit;
}

$hotel_id = $_POST['hotel_id'];
$hotel_name = $_POST['hotel_name'];
$postal_code = $_POST['postal_code'];
$address = $_POST['address'];
$building_name = $_POST['building_name'];
$phone_number = $_POST['phone_number'];
$email = $_POST['email'];
$url = $_POST['url'];
$description = $_POST['description'];
$photo = $_POST['photo'];


$stmt = $db->prepare('update HOTEL set HOTEL_NAME = :hotel_name, POSTAL_CODE = :postal_code, ADDRESS = :address, BUILDING_NAME = :building_name, PHONE_NUMBER = :phone_number, EMAIL = :email, URL = :url, DESCRIPTION = :description, PHOTO = :photo where HOTEL_ID = :hotel_id');
?>