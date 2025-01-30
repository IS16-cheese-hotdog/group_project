<?php include_once __DIR__ . '/inc/header.php'; ?>

<main>
    <a href="./user/search.php" class="search-link">検索</a>
    <section class="hero"></section>
</main>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
    margin: 0;
    padding: 0;
    background: url('./img/IMG_1941.JPG') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Arial', sans-serif;
    color: #333;
}

    .search-link {
        display: block;
        text-align: center;
        color: white;
        background: linear-gradient(45deg, #7da7c7, #5b7583);
        padding: 15px 20px;
        border-radius: 10px;
        font-size: 20px;
        text-decoration: none;
        transition: background 0.3s ease;
        margin-bottom: 20px;
    }

    .search-link:hover {
        background: linear-gradient(45deg, #5b7583, #4a5e6d);
    }

</style>

<?php include_once __DIR__ . '/inc/footer_main.php'; ?>