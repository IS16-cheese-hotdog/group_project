<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホテルプラン一覧</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #0056b3;
            /* ブルー系 */
            margin-top: 30px;
        }

        .add-button {
            display: block;
            margin: 20px auto;
            padding: 12px 20px;
            /* 縦のパディングを調整 */
            background: #007bff;
            /* ブルー */
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            /* フォントサイズを少し大きく */
            width: 200px;
            /* 横幅を固定にして大きすぎないように調整 */
            max-width: 100%;
            /* 最大幅は画面に合わせて伸びる */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .add-button:hover {
            background: #0056b3;
            /* ホバー時に濃いブルー */
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: #0056b3;
            /* ブルー系 */
            background: #f1f1f1;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background: #e1e1e1;
        }

        ul {
            list-style-type: none;
            padding: 0;
            max-width: 800px;
            margin: 30px auto;
        }

        li {
            background: #ffffff;
            margin: 15px 0;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #cce5ff;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        li:hover {
            transform: translateY(-5px);
        }

        .details {
            display: none;
            margin-top: 15px;
            background: #f0f8ff;
            padding: 10px;
            border: 1px solid #cce5ff;
            border-radius: 8px;
        }

        .details-button,
        .delete-button {
            position: absolute;
            top: 10px;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s ease;
        }

        .details-button {
            right: 90px;
            background: #007bff;
            /* ブルー系 */
            color: #fff;
        }

        .details-button:hover {
            background: #0056b3;
        }

        .delete-button {
            right: 10px;
            background: #dc3545;
            /* 赤系 */
            color: #fff;
        }

        .delete-button:hover {
            background: #c82333;
        }
    </style>
</head>

<body>
    <button onclick="history.back()" class="back-button">戻る</button>
    <h1>ホテルプラン一覧</h1>
    <a href="hotel_add_plan.php" class="add-button">プランを追加</a>
    <ul>
        <li>
            朝食付きプラン
            <button class="delete-button">削除</button>
            <div class="details">
                <p>内容: 朝食バイキング付き</p>
                <p>料金: ¥8,000/泊</p>
                <p>条件: 1泊2日以上の宿泊</p>
            </div>
        </li>
        <li>
            ディナー付きプラン
            <button class="delete-button">削除</button>
            <div class="details">
                <p>内容: 高級ディナー付き</p>
                <p>料金: ¥15,000/泊</p>
                <p>条件: 2泊以上の宿泊</p>
            </div>
        </li>
        <li>
            スペシャルプラン
            <button class="delete-button">削除</button>
            <div class="details">
                <p>内容: 朝食・夕食・スパ利用券付き</p>
                <p>料金: ¥20,000/泊</p>
                <p>条件: 連泊割引あり</p>
            </div>
        </li>
        <li>
            長期滞在プラン
            <button class="delete-button">削除</button>
            <div class="details">
                <p>内容: 長期滞在向け特別割引</p>
                <p>料金: ¥5,000/泊</p>
            </div>
        </li>
    </ul>
    <script>
        document.querySelectorAll('li').forEach(item => {
            const details = item.querySelector('.details');
            const deleteButton = item.querySelector('.delete-button');

            item.addEventListener('click', () => {
                if (details.style.display === 'block') {
                    details.style.display = 'none';
                } else {
                    details.style.display = 'block';
                }
            });

            deleteButton.addEventListener('click', (event) => {
                event.stopPropagation(); // リストアイテムのクリックイベントを無効化
                if (confirm('このプランを削除しますか？')) {
                    item.remove(); // プランを削除
                }
            });
        });
    </script>
</body>

</html>