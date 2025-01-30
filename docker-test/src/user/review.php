<?php
session_start();

// データベース接続情報
$host = 'mysql.pokapy.com:3307';
$dbname = 'php-docker-db';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('データベース接続に失敗しました: ' . $e->getMessage());
}

// `RESERVATION_ID` を POST で受け取る
if (!isset($_POST['reservation_id'])) {
    die("予約情報が見つかりません。");
}
$reservation_id = $_POST['reservation_id'];
$error = "";

// RESERVATION_IDを使ってユーザーIDとホテルID、ROOM_END_DATEを取得
try {
    $sql = "
        SELECT r.USER_ID, p.HOTEL_ID, r.ROOM_END_DATE 
        FROM RESERVATION r 
        JOIN PLAN p ON r.PLAN_ID = p.PLAN_ID
        WHERE r.RESERVATION_ID = :reservation_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
    $stmt->execute();

    // 結果を取得
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reservation) {
        die("予約情報が見つかりませんでした。");
    }

    $user_id = $reservation['USER_ID'];
    $hotel_id = $reservation['HOTEL_ID'];
    $room_end_date = $reservation['ROOM_END_DATE'];

    // 現在の日付とROOM_END_DATEを比較
    if (strtotime($room_end_date) >= time()) {
        $error = "予約期間が終了していないため、レビューを投稿することができません。";
    }

} catch (PDOException $e) {
    die('データベースクエリに失敗しました: ' . $e->getMessage());
}

// フォームが送信された場合
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? '';

    // バリデーション（評価は1〜5、コメント必須）
    if (!$rating || $rating < 1 || $rating > 5) {
        $error = "評価は1〜5の間で選択してください。";
    } elseif (empty($comment)) {
        $error = "コメントを入力してください。";
    } else {
        if (!$user_id || !$hotel_id) {
            $error = "ユーザー情報またはホテル情報が不足しています。";
        } else {
            // レビューをデータベースに登録
            $sql = "INSERT INTO REVIEW (USER_ID, HOTEL_ID, REVIEW_DATE, REVIEW_RATE, REVIEW_DETAIL) VALUES (:user_id, :hotel_id, NOW(), :rating, :comment)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':hotel_id', $hotel_id, PDO::PARAM_INT);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

            if ($stmt->execute()) {
                header("Location: user_check.php"); // 成功ページへリダイレクト
                exit();
            } else {
                $error = "レビューの投稿に失敗しました。";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レビュー投稿</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }
        .rating input {
            display: none;
        }
        .rating label {
            font-size: 2em;
            color: #ddd;
            cursor: pointer;
        }
        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: #f5b301;
        }
        .review-input {
            width: 100%;
            margin-top: 20px;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .submit-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
        }
        .submit-button:hover {
            background-color: #45a049;
        }
        .back-button {
            background-color: #ccc;
            color: #333;
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
        .back-button:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>レビューを書く</h1>
        <?php if ($error): ?>
            <p style="color: red;"> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?> </p>
        <?php endif; ?>
        <form action="review.php" method="post">
            <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation_id, ENT_QUOTES, 'UTF-8') ?>">
            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
                <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
            </div>
            <textarea class="review-input" name="comment" rows="4" placeholder="口コミを入力してください" required></textarea>
            <button type="submit" name="submit_review" class="submit-button">送信</button>
        </form>
        <button onclick="history.back()" class="back-button">前画面に戻る</button>
    </div>
</body>
</html>
