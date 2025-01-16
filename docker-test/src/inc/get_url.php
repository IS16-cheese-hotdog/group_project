<?php
function get_url() {
    try {
        $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
        return $url;
    } catch (Exception $e) {
        echo 'エラー: ' . $e->getMessage();
        return false;
    }
}
?>
