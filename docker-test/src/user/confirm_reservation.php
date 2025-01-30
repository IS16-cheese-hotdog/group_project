<?php
include_once __DIR__ . '/../inc/is_login.php';
include_once __DIR__ . '/../inc/db.php';

$db = db_connect();
try {
    $plan_id = $_POST["plan_id"];
    $user_id = $_SESSION["user_id"];
    $reservation_date = date('Y-m-d');
    $room_start_date = $_POST["room_start_date"];
    $room_end_date = $_POST["room_end_date"];
    $adults = $_POST["adults"];
    $childs = $_POST["children"];
    $infants = $_POST["infants"];

    $sql = "INSERT INTO RESERVATION (USER_ID, PLAN_ID, RESERVATION_DATE, ROOM_START_DATE, ROOM_END_DATE, ADULT, KID, INFANT) VALUES (:user_id, :plan_id, :reservation_date, :room_start_date, :room_end_date, :adults, :childs, :infants)";
    $result = $db->prepare($sql);
    $result->bindParam(":plan_id", $plan_id,PDO::PARAM_INT);
    $result->bindParam(":user_id", $user_id,PDO::PARAM_INT);
    $result->bindParam(":reservation_date", $reservation_date, PDO::PARAM_STR);
    $result->bindParam(":room_start_date", $room_start_date,PDO::PARAM_STR);
    $result->bindParam(":room_end_date", $room_end_date,PDO::PARAM_STR);
    $result->bindParam(":adults", $adults,PDO::PARAM_INT);
    $result->bindParam(":childs", $childs,PDO::PARAM_INT);
    $result->bindParam(":infants", $infants,PDO::PARAM_INT);
    if($result->execute()){
        echo "<script>alert('予約が完了しました'); window.location.href='user_check.php';</script>";
}
else{ 
    echo "<script>alert('予約に失敗しました'); window.location.href='user_check.php';</script>";
}
} catch (PDOException $e) {
    die('データベース接続に失敗しました: ' . $e->getMessage());
}