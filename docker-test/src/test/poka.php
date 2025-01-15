<?php
include_once __DIR__ . '/../inc/get_url.php';
include_once __DIR__ . '/../inc/get_prev_url.php';
$prev = get_prev_url();
echo $prev . "<br>";
$url = get_url();
echo $url . "<br>";

echo '__DIR__ : ' . __DIR__ . "<br>";
?>
