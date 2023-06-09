<?php
// configure the defines and then rename to cfg.php

define('SITE_BASE_URL', 'https://localhost');
define('SITE_BASE_DIR', 'C:\Users\Me\Projects\GrokBB\src\\');
define('SITE_BASE_APP', SITE_BASE_DIR . 'app' . DIRECTORY_SEPARATOR);
define('SITE_BASE_LIB', SITE_BASE_DIR . 'lib' . DIRECTORY_SEPARATOR);
define('SITE_BASE_GBB', SITE_BASE_LIB . 'grokbb' . DIRECTORY_SEPARATOR);

define('SITE_DATA_DIR', 'C:\Users\Me\Projects\GrokBB\upload\\');

define('DB_DSN', 'mysql:dbname=grokbb;host=localhost');
define('DB_PREFIX', 'gbb_');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'changeme');

define('CC_TEST_PK', 'pk_test_XXXXXXXXXXXXXXXXXXXXXXXX');
define('CC_TEST_SK', 'sk_test_XXXXXXXXXXXXXXXXXXXXXXXX');
define('CC_LIVE_PK', 'pk_live_XXXXXXXXXXXXXXXXXXXXXXXX');
define('CC_LIVE_SK', 'sk_live_XXXXXXXXXXXXXXXXXXXXXXXX');
define('CC_TURN_ON', 0);
define('CC_LIVE', 0);

define('CDNJS', 0);
define('DEBUG', 1);

spl_autoload_register(function ($class) {
    $classParts = explode('\\', $class);
    
    // handle the markdown-1.1.0 classes
    if ($classParts[0] == 'cebe') {
        $className = implode(DIRECTORY_SEPARATOR, array_slice($classParts, 2));
    } else {
        $className = end($classParts);
    }
    
    $libs = scandir(SITE_BASE_LIB);
    $classExists = false;
    
    foreach ($libs as $lib) {
        $classFile = SITE_BASE_LIB . $lib . DIRECTORY_SEPARATOR . $className . '.php';
        
        if ($lib != '.' && $lib != '..' && is_readable($classFile)) {
            require_once($classFile);
            $classExists = true;
        }
    }
    
    // if (!$classExists) {
    //     throw new Exception('The "' . $className . '" class does not exist.');
    // }
});

// required for the imageLib class to be found, since the file is not named the same
require_once(SITE_BASE_LIB . 'php-image-magician-1.0' . DIRECTORY_SEPARATOR . 'php_image_magician.php');

try {
    // establish a connection to the database
	$GLOBALS['db'] = new GrokBB\DB(DB_DSN, DB_USERNAME, DB_PASSWORD);
    
    if (DEBUG) {
        $GLOBALS['db']->query('SET PROFILING=1');
    }
    
    // store the session data in the database
    require_once(SITE_BASE_LIB . 'grokbb' . DIRECTORY_SEPARATOR . 'Session.php');
    
    $session = new GrokBB\Session($GLOBALS['db']);
    
    session_set_save_handler(
        array($session, 'open'),
        array($session, 'close'),
        array($session, 'read'),
        array($session, 'write'),
        array($session, 'destroy'),
        array($session, 'gc')
    );
         
    session_start();
    
    // log all the PHP errors in the database
    function logErrors($errLevel, $errMessage, $errFile, $errLine) {
        if (!($errLevel & error_reporting())) {
            return; // this error level is not being logged
        }
        
        $record = array(
            'level' => $errLevel,
            'message' => $errMessage,
            'file' => $errFile,
            'line' => $errLine,
            'time' => time()
        );
        
        if (isset($_SESSION['user'])) {
            $record['id_ur'] = $_SESSION['user']->id;
        }
        
        $GLOBALS['db']->insert('error', $record);
        
        return false;
    }
    
    set_error_handler('logErrors');
} catch (Exception $e) {
    if (basename($_SERVER['SCRIPT_NAME']) == 'index.php') {
        require(SITE_BASE_APP . 'error' . DIRECTORY_SEPARATOR . 'database.php');
        exit(); // don't allow the homepage to display underneath
    }
}
?>