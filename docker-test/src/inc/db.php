<?php
ob_start();
function db_connect()
{
    static $db = null;

    if ($db === null) {
        try {
            $host = 'mysql.pokapy.com:3307';
            $dbname = $_ENV['MYSQL_DATABASE'];
            $username = 'user';
            $password = $_ENV['MYSQL_ROOT_PASSWORD'];

            $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true,
            ]);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            return false;
        }
    }

    return $db;
}
?>