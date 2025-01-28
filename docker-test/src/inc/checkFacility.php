<?php
function checkFacility(int $facility)
{
    if ($facility === 1) {
        return '○';
    } else {
        return '×';
    }
}
