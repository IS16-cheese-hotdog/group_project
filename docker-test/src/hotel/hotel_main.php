<?php include_once __DIR__ . '/../inc/is_login.php'; ?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            /* ベースカラー */
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            /* 白背景 */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* 柔らかな影 */
            border-radius: 10px;
            /* 滑らかな角丸 */
        }

        h1 {
            text-align: center;
            color: #005073;
            /* 落ち着いた青 */
            font-size: 2em;
            margin-bottom: 20px;
        }

        .link-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            /* レスポンシブ対応 */
            gap: 15px;
            margin-top: 20px;
        }

        .link-container a {
            text-decoration: none;
            color: #ffffff;
            /* 白文字 */
            background-color: #008cba;
            /* メインボタンの色 */
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            /* ボタンの影 */
            transition: background-color 0.3s, transform 0.2s;
            /* ホバー時のアニメーション */
        }

        .link-container a:hover {
            background-color: #006f92;
            /* 濃い青色 */
            transform: translateY(-2px);
            /* ホバー時に浮く */
        }

        .link-container a:active {
            transform: translateY(0);
            /* クリック時に戻る */
            background-color: #004d66;
            /* 最も濃い青 */
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>マイページ</h1>
        <div class="link-container">
            <a href="hotel_check.php">予約状況の確認</a>
            <a href="hotel_info.php">ホテル情報登録更新</a>
        </div>
        <div class="link-container">
            <a href="../inc/logout.php">ログアウト</a>
        </div>
    </div>
</body>

</html>