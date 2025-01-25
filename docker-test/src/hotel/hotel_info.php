<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホテル情報更新</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            text-align: center;
        }
        h1 {
            font-size: 1.8em;
            color: #005c99;
            margin-bottom: 30px;
        }
        .link-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .link-container a {
            text-decoration: none;
            color: #ffffff;
            background-color: #5cbce6;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 1em;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .link-container a:hover {
            background-color: #499ab3;
            transform: translateY(-2px);
        }
        .buttons { text-align: center; margin-top: 20px; }
        .buttons button { padding: 10px 20px; font-size: 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }
        .buttons button:hover { background-color: #45a049; }
        .back-button { position: absolute; top: 20px; left: 20px; padding: 10px 20px; font-size: 16px; background-color: #d1e9ff; border: 1px solid #80c8ff; border-radius: 5px; color: #333; cursor: pointer; transition: background-color 0.3s; }
        .back-button:hover { background-color: #80c8ff; }
        @media (max-width: 600px) {
            .link-container a {
                padding: 10px 20px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ホテル情報更新</h1>
        <div class="link-container">
            <a href="hotel_update.php">登録情報更新</a>
            <a href="hotel_plan.php">プラン情報変更</a>
            <a href="hotel_room.php">部屋情報更新</a>
        </div>
        <div class="link-container">
            <button onclick="history.back()" class="back-button">戻る</button>
        </div>
    </div>
</body>
</html>
