<?php
session_start();
ob_start();
include_once(__DIR__ . '/../inc/db.php');

// データベース接続
$db = db_connect();
if ($db === false) {
    // エラーメッセージを表示
    die('<div style="color: red; font-weight: bold;">データベース接続に失敗しました。管理者に連絡してください。</div>');
}
/**
 * ホテル名をキーとしてホテル情報を取得する関数
 *
 * @param PDO $db PDOインスタンス
 * @param string $hotel_name ホテル名
 * @return array|null ホテル情報 (連想配列)、または null
 */
function get_hotel_by_name($db, $hotel_name) {
    try {
        $sql = "SELECT HOTEL_ID, ZIP, ADDRESS, EMAIL, PHONE_NUMBER, HOTEL_NAME, 
                       BUILDING_NAME, HOTEL_EXPLAIN, HOTEL_IMAGE
                FROM HOTEL
                WHERE HOTEL_NAME = :hotel_name LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':hotel_name', $hotel_name, PDO::PARAM_STR);
        $stmt->execute();
        $hotel = $stmt->fetch(PDO::FETCH_ASSOC);
        return $hotel ?: null;
    } catch (PDOException $e) {
        error_log("データベースエラー: " . $e->getMessage());
        return null;
    }
}

/**
 * ホテル情報を削除する関数
 *
 * @param PDO $db PDOインスタンス
 * @param string $hotel_name ホテル名
 * @return bool 削除成功時は true、失敗時は false
 */
function delete_hotel_by_name($db, $hotel_name) {
    try {
        $sql = "DELETE FROM HOTEL WHERE HOTEL_NAME = :hotel_name";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':hotel_name', $hotel_name, PDO::PARAM_STR);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("データベースエラー: " . $e->getMessage());
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホテル管理ページ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container input {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        .form-container button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .button-group button {
            flex: 1;
            margin: 0 5px;
            padding: 10px;
            font-size: 16px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button-group button:hover {
            background-color: #0056b3;
        }
        .hotel-info {
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .hotel-info h2 {
            margin-top: 0;
            color: #333;
        }
        .hotel-info p {
            margin: 10px 0;
        }
        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
        }
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>ホテル管理ページ</h1>
    <div class="container">
        <form method="POST" class="form-container">
            <input type="text" name="hotel_name" placeholder="ホテル名を入力してください" required>
            <button type="submit">検索</button>
        </form>
        <div class="button-group">
            <button onclick="location.href='add_hotel.php'">ホテルを追加</button>
            <button onclick="location.href='admin.php'">管理画面に戻る</button>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hotel_name'])) {
            $hotel_name = trim($_POST['hotel_name']);
            $hotel = get_hotel_by_name($db, $hotel_name);

            if ($hotel) {
                echo '<div class="hotel-info">';
                echo "<h2>ホテル情報</h2>";
                echo "<p><strong>ホテルID:</strong> " . htmlspecialchars($hotel['HOTEL_ID']) . "</p>";
                echo "<p><strong>郵便番号:</strong> " . htmlspecialchars($hotel['ZIP']) . "</p>";
                echo "<p><strong>住所:</strong> " . htmlspecialchars($hotel['ADDRESS']) . "</p>";
                echo "<p><strong>メールアドレス:</strong> " . htmlspecialchars($hotel['EMAIL']) . "</p>";
                echo "<p><strong>電話番号:</strong> " . htmlspecialchars($hotel['PHONE_NUMBER']) . "</p>";
                echo "<p><strong>ホテル名:</strong> " . htmlspecialchars($hotel['HOTEL_NAME']) . "</p>";
                echo "<p><strong>建物名:</strong> " . htmlspecialchars($hotel['BUILDING_NAME']) . "</p>";
                echo "<p><strong>ホテル説明:</strong> " . htmlspecialchars($hotel['HOTEL_EXPLAIN']) . "</p>";
                echo "<p><strong>ホテル画像:</strong> " . htmlspecialchars($hotel['HOTEL_IMAGE']) . "</p>";
                echo '<form method="POST" style="margin-top: 20px;">
                        <input type="hidden" name="delete_hotel_name" value="' . htmlspecialchars($hotel_name) . '">
                        <button type="submit" style="background-color: #f44336; color: white;">ホテルを削除</button>
                      </form>';
                echo '</div>';
            } else {
                echo '<div class="error-message">指定されたホテルは見つかりませんでした。</div>';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_hotel_name'])) {
            $hotel_name = trim($_POST['delete_hotel_name']);
            if (delete_hotel_by_name($db, $hotel_name)) {
                echo '<div class="success-message">ホテル情報を削除しました。</div>';
            } else {
                echo '<div class="error-message">削除に失敗しました。再試行してください。</div>';
            }
        }
        ?>
    </div>
</body>
</html>
