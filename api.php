<?php
// the GrokBB config
require_once('cfg.php');

$api = new GrokBB\API;

// verify a DB connection exists
if (!isset($GLOBALS['db'])) {
    echo $api->getResponse(false, (DEBUG) ? $e->getMessage() : 'Database Error #1');
}

// verify the API request came from GrokBB
$apiAuth = password_verify($_SESSION['apipass'], $_POST['apihash']);
if (!$apiAuth) { header('HTTP/1.1 401 Unauthorized'); exit(); }

if ($_POST['object']) {
    if ($_POST['method']) {
        try {
            $object = 'GrokBB\\' . $_POST['object'];
            $object = new $object();
            
            $params = array();
            
            if (isset($_POST['params'])) {
                if (!is_array($_POST['params'])) {
                    $params = explode(',', $_POST['params']);
                } else {
                    $params = $_POST['params'];
                }
            }
            
            echo call_user_func_array(array($object, $_POST['method']), $params);
        } catch (Exception $e) {
            echo $api->getResponse(false, (DEBUG) ? $e->getMessage() : 'API Error #3');
        }
    } else {
        echo $api->getResponse(false, (DEBUG) ? 'Method Required' : 'API Error #2');
    }
} else {
    echo $api->getResponse(false, (DEBUG) ? 'Object Required' : 'API Error #1');
}
?>