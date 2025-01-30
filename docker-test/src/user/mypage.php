<?php
include_once(__DIR__ . '/../inc/is_login.php'); 
include_once(__DIR__ . '/../inc/header.php');
?>

<head>
    <link rel="stylesheet" href="./css/mypage.css">
</head>

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
            <div class="tab" onclick="window.location.href='search.php?from=mypage'">検索</div> <!-- 'mypage' から来たことを示す -->
        </div>
        <div class="link-container">
            <a href="/inc/logout.php">ログアウト</a>
        </div>
        <div id="info" class="tab-content">
            <h2>登録情報確認</h2>
            <!-- 登録情報確認の内容 -->
        </div>
    </div>

    <div id="withdraw-tab">退会</div>

    <script>
        document.getElementById('withdraw-tab').onclick = function(event) {
            event.preventDefault();
            const popup = document.createElement('div');
            popup.className = 'popup';
            popup.innerHTML = ` 
                <p>本当に退会しますか？</p>
                <button id="confirm-yes">はい</button>
                <button id="confirm-no">いいえ</button>
            `;
            document.body.appendChild(popup);

            document.getElementById('confirm-yes').addEventListener('click', function() {
                window.location.href = 'main.php';
            });

            document.getElementById('confirm-no').addEventListener('click', function() {
                document.body.removeChild(popup);
            });
        };

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
