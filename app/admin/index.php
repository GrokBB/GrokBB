<?php
if (!isset($_SESSION['user']) || $_SESSION['user']->id != 1) {
    header('Location: ' . SITE_BASE_URL);
}

echo 'Remote IP Address: ' . $_SERVER['REMOTE_ADDR'] . '<br />';
echo 'Foward IP Address: ' . $_SERVER['HTTP_X_FORWARDED_FOR'] . '<br />';
?>