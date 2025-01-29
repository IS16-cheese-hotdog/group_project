<?php include_once __DIR__ . '/../inc/is_hotel.php'; ?>
<?php include_once __DIR__ . '/../inc/header.php'; ?>
<head>
    <link rel="stylesheet" href="./css/hotel_main.css">
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
<?php include_once __DIR__ . '/../inc/footer.php'; ?>
