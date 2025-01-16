<?php

$host = 'mysql.pokapy.com:3307'; // MySQLコンテナのサービス名
$dbname = $_ENV['MYSQL_DATABASE'];
$username = 'root';
$password = $_ENV['MYSQL_ROOT_PASSWORD'];

# 新しいPDOオブジェクトを作成し、MySQLデータベースに接続
$db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password);

# SQL　文を実行
$stmt = $db->prepare('SELECT * FROM USER');
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $result) {
    echo '<p>' . $result['USER_ID'] . '. ' . $result['USER_NAME'] . '</p>' . PHP_EOL;
}