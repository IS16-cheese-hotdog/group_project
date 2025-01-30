<?php
include_once __DIR__ . '/../inc/db.php';
session_start();
$db = db_connect() ;

$reservation_id = $_POST['reservation_id'];

try {
    $sql = 'SELECT * FROM RESERVATION LEFT JOIN PLAN ON RESERVATION.PLAN_ID = PLAN.PLAN_ID LEFT JOIN HOTEL ON HOTEL.HOTEL_ID = PLAN.PLAN_ID WHERE reservation_id = :reservation_id';
    $stmt = $db->prepare($sql) ;
    $stmt->bindParam(':reservation_id', $reservation_id) ;
    $stmt->execute() ;
    $result = $stmt->fetch(PDO::FETCH_ASSOC) ;

} catch (Exception $e) {
    echo 'エラー: ' . $e->getMessage();
}

var_dump($result) ;?>