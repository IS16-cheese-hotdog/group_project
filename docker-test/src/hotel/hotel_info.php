<?php
include_once __DIR__ . '/../inc/is_login.php';
include_once __DIR__ . '/../inc/header.php';
?>

<head>
    <link rel="stylesheet" href="./css/hotel_info.css">
</head>

<body>
    <div class="container">
        <h1>ホテル情報更新</h1>
        <div class="link-container">
            <a href="hotel_update.php">登録情報更新</a>
            <a href="hotel_plan.php">プラン情報変更</a>
            <a href="hotel_room.php">部屋情報更新</a>
        </div>
    </div>

</body>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>