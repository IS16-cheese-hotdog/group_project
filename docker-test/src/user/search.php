<?php
session_start();
include_once __DIR__ . '/../inc/header.php'; ?>
<link rel="stylesheet" href="style.css">
<style>
/* 地域と都道府県のセレクトボックスを統一 */
.search-container select {
    width: 100%; /* フォームの他の要素と統一 */
    padding: 15px; /* ボックスを大きく */
    margin: 10px 0;
    border: 2px solid #4682b4; /* 枠の色を青に */
    border-radius: 8px; /* 角を少し丸く */
    font-size: 18px; /* 文字サイズを大きく */
    background-color: white;
    color: #333;
}

/* 選択時の強調 */
.search-container select:focus {
    border-color: #1e5b91; /* 選択時に濃い青 */
    outline: none;
    box-shadow: 0 0 5px rgba(30, 91, 145, 0.5); /* 軽いシャドウで強調 */
}

/* 地域と都道府県のセレクトを並べる */
.region-container {
    display: flex;
    gap: 15px; /* 適度な間隔 */
}

.region-container select {
    flex: 1; /* 均等に並ぶ */
}

/* フォーム全体のスタイル */
.search-container {
    max-width: 600px;
    margin: 30px auto;
    padding: 20px;
    border: 2px solid #4682b4;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
}

/* 検索ボタン */
.search-button {
    width: 100%;
    padding: 12px;
    font-size: 18px;
    background-color: #4682b4;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

.search-button:hover {
    background-color: #1e5b91;
}
</style>
<body>
    <div class="search-container">
        <h1>ホテル検索</h1>
        <form action="results.php" method="post">
        <div class="region-container">
    <label for="region">地域を選択してください:</label>
    <select id="region" name="region" onchange="updatePrefectureOptions()">
        <option value="">選択してください</option>
        <option value="北海道">北海道</option>
        <option value="東北">東北</option>
        <option value="関東">関東</option>
        <option value="東京">東京</option>
        <option value="中部">中部</option>
        <option value="名古屋">名古屋</option>
        <option value="北陸">北陸</option>
        <option value="関西">関西</option>
        <option value="大阪">大阪</option>
        <option value="中国">中国</option>
        <option value="四国">四国</option>
        <option value="九州">九州</option>
        <option value="福岡">福岡</option>
        <option value="沖縄">沖縄</option>
    </select>

    <select id="prefecture" name="prefecture" style="display: none;">
        <option value="">選択してください</option>
    </select>
</div>


            <label for="max_people">最大人数:</label>
            <input type="number" name="max_people" id="max_people" min="1"><br>

            <label for="charge">料金:</label>
            <input type="number" name="charge" id="charge" min="0"><br>

            <label for="eat">食事:</label>
            <input type="radio" name="eat" value="1" id="eat_yes">
            <label for="eat_yes">可</label>
            <input type="radio" name="eat" value="0" id="eat_no">
            <label for="eat_no">不可</label><br>

            <label for="bed_number">ベッド数:</label>
            <input type="number" name="bed_number" id="bed_number" min="0"><br>

            <label for="bathroom">お風呂:</label>
            <input type="radio" name="bathroom" value="1" id="bathroom_yes">
            <label for="bathroom_yes">可</label>
            <input type="radio" name="bathroom" value="0" id="bathroom_no">
            <label for="bathroom_no">不可</label><br>

            <label for="dryer">ドライヤー:</label>
            <input type="radio" name="dryer" value="1" id="dryer_yes">
            <label for="dryer_yes">可</label>
            <input type="radio" name="dryer" value="0" id="dryer_no">
            <label for="dryer_no">不可</label><br>

            <label for="tv">テレビ:</label>
            <input type="radio" name="tv" value="1" id="tv_yes">
            <label for="tv_yes">可</label>
            <input type="radio" name="tv" value="0" id="tv_no">
            <label for="tv_no">不可</label><br>

            <label for="wifi">WI-FI:</label>
            <input type="radio" name="wifi" value="1" id="wifi_yes">
            <label for="wifi_yes">可</label>
            <input type="radio" name="wifi" value="0" id="wifi_no">
            <label for="wifi_no">不可</label><br>

            <label for="pet">ペット:</label>
            <input type="radio" name="pet" value="1" id="pet_yes">
            <label for="pet_yes">可</label>
            <input type="radio" name="pet" value="0" id="pet_no">
            <label for="pet_no">不可</label><br>

            <label for="smoking">喫煙:</label>
            <input type="radio" name="smoking" value="1" id="smoking_yes">
            <label for="smoking_yes">可</label>
            <input type="radio" name="smoking" value="0" id="smoking_no">
            <label for="smoking_no">不可</label><br>

            <label for="refrigerator">冷蔵庫:</label>
            <input type="radio" name="refrigerator" value="1" id="refrigerator_yes">
            <label for="refrigerator_yes">可</label>
            <input type="radio" name="refrigerator" value="0" id="refrigerator_no">
            <label for="refrigerator_no">不可</label><br>

            <button type="submit" class="search-button">検索</button>
        </form>
    </div>

    <script>
        const regionPrefectures = {
            "東北": ["青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県"],
            "関東": ["茨城県", "栃木県", "群馬県", "埼玉県", "千葉県", "神奈川県"],
            "中部": ["山梨県", "長野県", "岐阜県", "静岡県", "三重県"],
            "北陸": ["富山県", "石川県", "福井県", "新潟県"],
            "関西": ["滋賀県", "京都府", "兵庫県", "奈良県", "和歌山県"],
            "中国": ["鳥取県", "島根県", "岡山県", "広島県", "山口県"],
            "四国": ["徳島県", "香川県", "愛媛県", "高知県"],
            "九州": ["佐賀県", "長崎県", "熊本県", "大分県", "宮崎県", "鹿児島県"]
        };

        function updatePrefectureOptions() {
            const region = document.getElementById('region').value;
            const prefectureSelect = document.getElementById('prefecture');

            prefectureSelect.innerHTML = '<option value="">選択してください</option>';
            if (region && regionPrefectures[region]) {
                prefectureSelect.style.display = 'inline-block';
                regionPrefectures[region].forEach(prefecture => {
                    const option = document.createElement('option');
                    option.value = prefecture;
                    option.textContent = prefecture;
                    prefectureSelect.appendChild(option);
                });
            } else {
                prefectureSelect.style.display = 'none';
            }
        }
    </script>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>
