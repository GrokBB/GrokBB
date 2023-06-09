<?php
$nameCleaned = GrokBB\Util::sanitizeBoard($_SESSION['gbbboard']);
$board = $GLOBALS['db']->getOne('board', array('name' => $nameCleaned));

if ($board) {    
    if (isset($_COOKIE['board_prefs'])) {
        $bp = json_decode($_COOKIE['board_prefs'], true);
    } else {
        $bp = array();
    }
    
    $reqParts = explode('/', $_SERVER['REQUEST_URI']);
    $reqValue = $reqParts[count($reqParts) - 1];
    $reqType  = $reqParts[count($reqParts) - 2];
    
    // all data sanitizing is done when we read the cookie
    switch($reqType) {
        case 'sort' :
            $bp[$board->id]['sort'] = $reqValue;
            
            if ($reqValue == 'qry') {
                $bp[$board->id]['sqry']['text'] = $_POST['text'];
                $bp[$board->id]['sqry']['type'] = $_POST['type'];
                $bp[$board->id]['sqry']['user'] = $_POST['user'];
                $bp[$board->id]['sqry']['sort'] = $_POST['sort'];
            } else if (isset($bp[$board->id]['sqry'])) {
                unset($bp[$board->id]['sqry']);
            }
            
            break;
        case 'bcat' :
            $bp[$board->id]['bcat'] = $reqValue;
            break;
        case 'btag' :
            $bp[$board->id]['btag'] = $reqValue;
            break;
        case 'rply' :
            $bp[$board->id]['rply'] = $reqValue;
            break;
    }
    
    setcookie('board_prefs', json_encode($bp), time() + (86400 * 365), '/', '', false, true);
}

if (($reqValue != 'qry' && $reqType != 'rply') || $_POST['home']) {
    header('Location: ' . SITE_BASE_URL . '/g/' . $_SESSION['gbbboard']);
}
?>