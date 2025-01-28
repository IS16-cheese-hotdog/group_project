<?php
// パラメータからerr_msgを取得し表示
if (isset($_GET['err_msg'])) {
    echo '<p style="color: red;">' . $_GET['err_msg'] . '</p>';
}
?>