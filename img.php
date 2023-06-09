<?php
// the GrokBB config
require_once('cfg.php');

$uid = (isset($_GET['uid'])) ? (int) $_GET['uid'] : 0;
$bid = (isset($_GET['bid'])) ? (int) $_GET['bid'] : 0;
$vimeo = (isset($_GET['vimeo'])) ? (int) $_GET['vimeo'] : 0;
$ytube = (isset($_GET['ytube'])) ? preg_replace('/^[A-Za-z0-9_-]$/', '', $_GET['ytube']) : 0;

if ($uid) {
    // get the user's avatar
    $imgPath = SITE_DATA_DIR . 'users' . DIRECTORY_SEPARATOR . $uid . DIRECTORY_SEPARATOR . 'avatar.png';
    
    header('Content-Type: image/png');
    
    // default to the system avatar when one doesn't exist
    if (is_readable($imgPath) === false) {
        echo file_get_contents(SITE_BASE_DIR . 'img' . DIRECTORY_SEPARATOR . 'avatar.png');
    } else {
        echo file_get_contents($imgPath);
    }
    
    exit();
} else if ($bid) {
    $imgPath = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $bid . DIRECTORY_SEPARATOR;
    $image = (isset($_GET['img'])) ? preg_replace('/[^\da-z\._]/', '', $_GET['img']) : false;
    $category = (isset($_GET['cat'])) ? preg_replace('/[^\d]/', '', $_GET['cat']) : false;
    $badge = (isset($_GET['bad'])) ? preg_replace('/[^\d]/', '', $_GET['bad']) : false;
    
    if ($image) {
        $imgPath .= 'images' . DIRECTORY_SEPARATOR . $image;
    } else if ($category) {
        header('Content-Type: image/png');
        $imgPath .= 'categories' . DIRECTORY_SEPARATOR . $category . '.png';
    } else if ($badge) {
        header('Content-Type: image/svg+xml');
        $imgPath .= 'badges' . DIRECTORY_SEPARATOR . $badge . '.svg';
    } else {
        header('Content-Type: image/png');
        $imgPath .= 'header.png';
        
        if (is_readable($imgPath) === false) {
            $imgPath = SITE_BASE_DIR . 'img' . DIRECTORY_SEPARATOR . 'header.png';
        }
    }
    
    if (is_readable($imgPath) === false) {
        header('HTTP/1.1 404 Not Found');
    } else {
        echo file_get_contents($imgPath);
    }
    
    exit();
} else if ($ytube) {
    $imgPath = 'https://img.youtube.com/vi/' . $ytube . '/default.jpg';
    $imgBlob = file_get_contents($imgPath);
    
    if ($imgBlob) {
        echo $imgBlob;
        exit();
    }
} else if ($vimeo) {
    $imgData = unserialize(file_get_contents('https://vimeo.com/api/v2/video/' . $vimeo . '.php'));
    
    if ($imgData) {
        $imgPath = $imgData[0]['thumbnail_small'];
        $imgBlob = file_get_contents($imgPath);
        
        if ($imgBlob) {
            echo $imgBlob;
            exit();
        }
    }
}

header('HTTP/1.1 404 Not Found');
?>