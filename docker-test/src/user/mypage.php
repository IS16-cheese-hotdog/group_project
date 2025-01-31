<?php
include_once(__DIR__ . '/../inc/is_login.php'); 
include_once(__DIR__ . '/../inc/header.php');
?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #01579b;
        }
        .link-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .link-container a {
            text-decoration: none;
            color: #ffffff;
            background-color: #0288d1;
            padding: 15px 30px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .link-container a:hover {
            background-color: #0277bd;
            transform: scale(1.05);
        }
        .tab {
            display: inline-block;
            padding: 12px 25px;
            cursor: pointer;
            background-color: #0288d1;
            color: #ffffff;
            border-radius: 5px;
            margin: 5px;
            transition: background-color 0.3s, box-shadow 0.2s;
        }
        .tab:hover {
            background-color: #0277bd;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
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
            justify-content: space-around;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .notification-wrapper {
            margin-top: 20px;
            padding: 20px;
            background-color: #e1f5fe;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .notification h2 {
            margin-top: 0;
            color: #01579b;
        }
    </style>
</head>
<body>
    <h1>マイページ</h1>
    <div class="container">
        <div class="notification-wrapper">
            <div class="notification">
                <h2>お知らせ</h2>
                <p>2023年10月1日: システムメンテナンスのお知らせ</p>
                <p>2023年10月15日: 新機能追加のお知らせ</p>
            </div>
        </div>
        <div class="tabs">
            <div class="tab" onclick="window.location.href='user_check.php'">予約確認</div>
            <div class="tab" onclick="window.location.href='user_change.php'">登録情報更新</div>
            <div class="tab" onclick="window.location.href='search.php?from=mypage'">検索</div> 
        </div>
        <div class="link-container">
            <a href="/inc/logout.php">ログアウト</a>
        </div>
        <div id="info" class="tab-content">
            <h2>登録情報確認</h2>
        </div>
    </div>

    <script>
        function updateNotifications(data) {
            const notificationContainer = document.querySelector('.notification');
            notificationContainer.innerHTML = '<h2>お知らせ</h2>';
            data.slice(0, 5).forEach(item => {
                const p = document.createElement('p');
                p.textContent = item;
                notificationContainer.appendChild(p);
            });
        }
    </script>
<?php include_once(__DIR__ . '/../inc/footer.php'); ?>
