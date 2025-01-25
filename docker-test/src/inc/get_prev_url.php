<?php
function get_prev_url()
{
    try {
        if (!isset($_SERVER['HTTP_REFERER'])) {
            $prev = '/';
        } else {
            $prev = $_SERVER['HTTP_REFERER'];
        }
        $path = parse_url($prev, PHP_URL_PATH);
        return $path;
    } catch (Exception $e) {
        echo 'エラー: ' . $e->getMessage();
        return false;
    }
}
?>