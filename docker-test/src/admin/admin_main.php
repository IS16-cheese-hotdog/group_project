<?php
include_once __DIR__ . '/../inc/is_admin.php';


?>

<?php include_once __DIR__ . '/../inc/header.php'; ?>

</head>

<head>
    <link rel="stylesheet" href="./css/admin_main.css">
</head>

<body>
    <div class="container">
        <h1>管理者用ページ</h1>
        <div class="tabs">
            <div class="tab" onclick="window.location.href='admin_user_manager.php'">会員管理</div>
            <div class="tab" onclick="window.location.href='admin_hotel_manager.php'">ホテル管理</div>
        </div>
        <div class="link-container">
            <a href="/inc/logout.php">ログアウト</a>
        </div>
        <div id="info" class="tab-content">
            <h2>登録情報確認</h2>
            <!-- 登録情報確認の内容 -->
        </div>
    </div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>
