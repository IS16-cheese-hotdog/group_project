<?php include_once __DIR__ . '/inc/header.php'; ?>

<main>
    <a href="./user/search.php" class="search-link">検索</a>
    <section class="hero">
        <div class="slideshow-container">
            <div class="slide fade">
                <img src="./img/IMG_1941.JPG" alt="Image 1">
            </div>
            <div class="slide fade">
                <img src="./img/IMG_0354.JPG" alt="Image 2">
            </div>
            <div class="slide fade">
                <img src="./img/IMG_2185.JPG" alt="Image 3">
            </div>
            <div class="slide fade">
                <img src="./img/IMG_0119.JPG" alt="Image 4">
            </div>
        </div>
    </section>
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
        background: #f0f0f0;
        font-family: 'Arial', sans-serif;
        color: #333;
        overflow: hidden; /* スクロールを無効化 */
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
        margin-top: 0; /* ボタンと画像間の隙間をなくす */
        z-index: 10; /* ボタンを画像の上に表示 */
        position: relative; /* スライドショーの上に重なる */
    }

    .search-link:hover {
        background: linear-gradient(45deg, #5b7583, #4a5e6d);
    }

    .hero {
        width: 100%;
        height: 100vh; /* 画面全体を使って画像を表示 */
        position: relative;
        overflow: hidden; /* スクロールを無効化 */
    }

    .slideshow-container {
        display: flex;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        transition: transform 1s ease-in-out; /* スライド効果 */
    }

    .slide {
        min-width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* 画像がコンテナを覆い尽くす */
        filter: brightness(0.9) contrast(1.1); /* 明るさとコントラストを調整 */
    }

    .search-link {
        z-index: 20; /* ボタンが画像より前に表示されるように */
    }
</style>

<script>
    let slideIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const slideshowContainer = document.querySelector('.slideshow-container');

    function showSlides() {
        slideIndex = (slideIndex + 1) % slides.length;
        const offset = -100 * slideIndex; // スライドの移動量

        slideshowContainer.style.transform = `translateX(${offset}%)`; // 横にスライド

        setTimeout(showSlides, 10000); // 5秒後に次の画像を表示
    }

    showSlides(); // 初期状態でスライドを表示
</script>

<?php include_once __DIR__ . '/inc/footer_main.php'; ?>