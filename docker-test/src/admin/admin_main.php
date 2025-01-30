<?php
include_once __DIR__ . '/../inc/is_admin.php';


?>

<?php include_once __DIR__ . '/../inc/header.php'; ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f7ff; /* 背景色をデフォルトの#e6f7ffに設定 */
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .link-container {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }
        .link-container a {
            text-decoration: none;
            color: #fff;
            background-color: #007bff; /* ログアウトボタンに青系統の色 */
            padding: 15px 30px;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 16px;
        }
        .link-container a:hover {
            background-color: #0056b3; /* ホバー時の色 */
        }
        .tab {
            display: inline-block;
            padding: 12px 25px;
            cursor: pointer;
            background-color: #007BFF; /* 鮮やかなブルー */
            color: white;
            border-radius: 5px;
            margin: 5px;
            transition: background-color 0.3s;
            font-size: 16px;
        }
        .tab:hover {
            background-color: #0056b3;
        }
        .tab-content {
            display: none;
            margin-top: 20px;
        }
        .tab-content.active {
            display: block;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .tab, .link-container a {
            text-align: center;
        }
    </style>
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
