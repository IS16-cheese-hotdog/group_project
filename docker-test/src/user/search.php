<?
session_start();

include_once __DIR__ . '/../inc/header.php'; ?>
    <link rel="stylesheet" href="style.css">
<body>
    <h1>ホテル検索フォーム</h1>
    <form action="results.php" method="post">
        <label for="address">県名:</label>
        <input type="text" name="address" id="address"><br>

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
<?php include_once __DIR__ . '/../inc/footer.php'; ?>