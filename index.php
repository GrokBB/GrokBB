<?php
// the GrokBB config
require_once('cfg.php');

// clean the URL by removing the script name
$urlclean = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);

// parse the URL for boards differently
$urlparts = explode('/', rtrim($urlclean, '/'));

if (isset($urlparts[1]) && $urlparts[1] == 'g') {
    if (count($urlparts) == 3 || substr($urlparts[3], 0, 1) == '?') {
        // default boards to the home template
        $urlclean = str_replace('/g/', '/g/home/', $urlclean);
        
        if (isset($urlparts[3])) {
            // remove the URL params so it's not seen as a board's page
            $urlclean = str_replace('/' . $urlparts[3], '', $urlclean);
        }
    } else {
        // add the template path before the board's name
        $urlboard = $urlparts[2];
        $urlparts[2] = $urlparts[3];
        $urlparts[3] = $urlboard;
        $urlclean = implode('/', $urlparts);
    }
}

// parse the URL and ID
$urlparts = explode('/', trim($urlclean, '/'));

if (isset($urlparts[3])) {
    // a board's page
    $objectid = $urlparts[3];
    $gbbboard = $urlparts[2];
} else if (isset($urlparts[2])) {
    // a board's home or a GrokBB page
    $objectid = ($urlparts[0] == 'g') ? 0 : $urlparts[2];
    $gbbboard = ($urlparts[0] == 'g') ? $urlparts[2] : 0;
} else {
    $objectid = 0;
    $gbbboard = 0;
}

// parse the requested template
$pagerqst = (isset($urlparts[0])) ? $urlparts[0] . ((isset($urlparts[1])) ? '/' . $urlparts[1] : '') : '';
$template = ($pagerqst === '') ? 'home/index' : $pagerqst;
$template = SITE_BASE_APP . str_replace('/', DIRECTORY_SEPARATOR, $template)  . '.php';

// make sure the template exists and it's readable
if (is_readable($template) === false) {
    $template = SITE_BASE_APP . 'error' . DIRECTORY_SEPARATOR . 'template.php';
}

// generates the API authentication
if (!isset($_SESSION['apipass'])) {
    $_SESSION['apipass'] = microtime();
    $_SESSION['apihash'] = password_hash($_SESSION['apipass'], PASSWORD_DEFAULT);
}

// automatic login via cookies
if (isset($_COOKIE['uid'])) {
    $user = new GrokBB\User;
    $user = $user->getByHash($_COOKIE['uid']);
    $_SESSION['user'] = GrokBB\Util::sanitizeSession($user);
} else if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = null;
}

// set the default include settings
$GLOBALS['includePicker'] = false;
$GLOBALS['includeEditor'] = false;
$GLOBALS['includeCharts'] = false;
$GLOBALS['includeSlides'] = false;
$GLOBALS['includePlayer'] = false;
$GLOBALS['includeStripe'] = false;
$GLOBALS['displayHeader'] = true;

$_SESSION['pagerqst'] = $pagerqst;
$_SESSION['objectid'] = $objectid;
$_SESSION['template'] = $template;

$_SESSION['gbbboard'] = $gbbboard;
$_SESSION['gbbstats'] = $GLOBALS['db']->getOne('stats');

// display the template
require($_SESSION['template']);
?>