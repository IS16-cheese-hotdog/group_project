<?php
if (php_sapi_name() === 'cli') {
    die("このスクリプトはWebサーバーを介して実行してください。");
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホテル予約</title>
    <link rel="stylesheet" href="/inc/css/header.css">
</head>

<body>
<header>
    <div>
        <button onclick="history.back()" class="back-button">戻る</button>
        <div class="site-name">
            <a href="/" style="color: #ffffff; text-decoration: none;">サイト名(仮)</a>
        </div>
        <?php if (isset($_SESSION['user_id'])) : ?>
            <div class="buttons">
                <?php if(isset($_SESSION['admin_id'])) : ?>
                    <button onclick="location.href='/admin/admin_main.php'" class="mypage-button">マイページ</button>
                <?php elseif(isset($_SESSION['hotel_id'])) : ?>
                    <button onclick="location.href='/hotel/hotel_main.php'" class="mypage-button">マイページ</button>
                <?php else : ?>
                    <button onclick="location.href='/user/mypage.php'" class="mypage-button">マイページ</button>
                <?php endif; ?>
            </div>
        <?php elseif (!isset($_SESSION['user_id'])) : ?>
            <div class="buttons">
                <button onclick="location.href='/login.php'" class="mypage-button">ログイン</button>
            </div>
        <?php endif; ?>
    </div>
</header>