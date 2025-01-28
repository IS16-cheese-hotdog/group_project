<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホテル予約</title>
    <style>
        header {
            position: relative;
            background-color: #3e5060;
            /* より落ち着いた色 */
            padding: 20px 40px;
        }

        .buttons {
            text-align: center;
            margin-top: 20px;
        }

        .buttons button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .buttons button:hover {
            background-color: #45a049;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #d1e9ff;
            border: 1px solid #80c8ff;
            border-radius: 5px;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #80c8ff;
        }

        .site-name {
            color: #ffffff;
            /* 白文字 */
            font-size: 1.5em;
            /* フォントサイズを少し大きく */
            font-weight: bold;
            /* 太字 */
            text-align: center;
        }

        footer {
            background-color: #3e5060;
            /* 一貫した色味 */
            color: white;
            text-align: center;
            padding: 20px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <header>
        <div>
            <button onclick="history.back()" class="back-button">戻る</button>
            <div class="site-name">サイト名(仮)</div>
        </div>
    </header>