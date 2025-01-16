<?php

echo '<h1>Hello World</h1>';
echo '<a href="test/poka.php">poka</a>' . '<br>';
echo "<a href='hotel/hotel_update.php'>update</a>" . '<br>';

?>
<form onsubmit="return check()" id="form"  method="post">
    <input type="text" name="name" value="test" id="text">
    <input type="submit" value="submit">
</form>

<script>  
    function check() {
        event.preventDefault();
        var form = document.getElementById('form');
        var text = document.getElementById('text').value;
        form.action = 'test/poka.php';
        if (document.body.contains(document.querySelector('.popup'))) {
            document.body.removeChild(document.querySelector('.popup'));
        }
        const popup = document.createElement('div');
        popup.className = 'popup';
        popup.innerHTML = `
            <p>更新しますか？</p>
            <p>名前: ${text}</p>
            <button id="confirm-yes">はい</button>
            <button id="confirm-no">いいえ</button>
        `;
        document.body.appendChild(popup);

        document.getElementById('confirm-yes').addEventListener('click', function() {
            form.submit();
        });

        document.getElementById('confirm-no').addEventListener('click', function() {
            document.body.removeChild(popup);
        });
    }
</script>

