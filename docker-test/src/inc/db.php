<?php
# MySQLデータベースに接続
function db_connect() {
    try {
        $host = 'mysql.pokapy.com:3307'; // サーバーのドメインとポート番号
        $dbname = $_ENV['MYSQL_DATABASE'];
        $username = 'user';
        $password = $_ENV['MYSQL_ROOT_PASSWORD'];

        # 新しいPDOオブジェクトを作成し、MySQLデータベースに接続
        $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password);
        return $db;
    } catch (PDOException $e) {
        echo '接続エラー: ' . $e->getMessage();
        return false;
    }
}
?>

