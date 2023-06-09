<?php
ob_start(); // we need to make sure the common header did not redirect before displaying anything

if (CDNJS) {
    // the cached CDN versions
    $resUK = '//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3';
    $resJQ = '//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.0';
    $resSM = '//cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0';
    $resCJ = '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4';
    $resCM = '//cdnjs.cloudflare.com/ajax/libs/codemirror/5.13.2';
    // $resMK = '//cdnjs.cloudflare.com/ajax/libs/marked/0.3.5';
} else {
    $resUK = SITE_BASE_URL . '/lib/uikit-2.26.3';
    $resJQ = SITE_BASE_URL . '/lib/jquery-2.2.0';
    $resSM = SITE_BASE_URL . '/lib/spectrum-1.8.0';
    $resCJ = SITE_BASE_URL . '/lib/chart.js-2.1.4/dist';
    $resCM = SITE_BASE_URL . '/lib/codemirror-5.13.2';
    // $resMK = SITE_BASE_URL . '/lib/marked-0.3.5';
}

if ($_SESSION['user']) {
    $announcement = $GLOBALS['db']->getOne('board_announcement ba INNER JOIN ' . DB_PREFIX . 'user_board ub ON ba.id_bd = ub.id_bd ' . 
        'LEFT JOIN ' . DB_PREFIX . 'user_board_announcement uba ON ba.id = uba.id_ba AND uba.id_ur = ' . $_SESSION['user']->id, 
        array('ub.id_ur' => $_SESSION['user']->id, 'ub.deleted' => 0, 'ba.sent' => array('> ub.added'), 'uba.id' => array('IS NULL')));
    
    $inboxMessage = $GLOBALS['db']->getOne('message', array('id_to' => $_SESSION['user']->id, 'deleted' => 0, 'read' => 0, 'rcvd' => array('>', 0)));
    $myBoards = $GLOBALS['db']->getAll('board', array('id_ur' => $_SESSION['user']->id), 'name ASC');
    $fvBoards = $GLOBALS['db']->getAll('board b INNER JOIN ' . DB_PREFIX . 'user_board ub ON b.id = ub.id_bd', array('ub.id_ur' => $_SESSION['user']->id, 'ub.deleted' => 0), 'ub.added DESC', false, 5);
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="GrokBB: Simple, Beautiful, Community Building" />
    
    <title>GrokBB: <?php echo ($_SESSION['pagerqst']) ? ucwords(str_replace('/', ' > ', $_SESSION['pagerqst'])) : 'Simple, Beautiful, Community Building'; ?></title>
	
	<link rel="stylesheet" href="<?php echo $resUK; ?>/css/uikit.almost-flat.min.css" />
	<link rel="stylesheet" href="<?php echo $resUK; ?>/css/components/form-file.almost-flat.min.css" />
	<link rel="stylesheet" href="<?php echo $resUK; ?>/css/components/notify.almost-flat.min.css" />
	<link rel="stylesheet" href="<?php echo $resUK; ?>/css/components/progress.almost-flat.min.css" />
	<link rel="stylesheet" href="<?php echo $resUK; ?>/css/components/tooltip.almost-flat.min.css" />
	<link rel="stylesheet" href="<?php echo $resUK; ?>/css/components/upload.almost-flat.min.css" />
	<link rel="stylesheet" href="<?php echo SITE_BASE_URL; ?>/lib/jquery-ui-autocomplete-1.11.4/jquery-ui.min.css" />
	<link rel="stylesheet" href="<?php echo SITE_BASE_URL; ?>/lib/tag-handler-1.3.1/css/jquery.taghandler.css" />
	<!-- <link rel="stylesheet" href="<?php echo SITE_BASE_URL; ?>/lib/arrowchat/external.php?type=css" /> -->
	<link rel="stylesheet" href="<?php echo SITE_BASE_URL; ?>/lib/grokbb/css/grokbb.css.php" />
	
	<script src="<?php echo $resJQ; ?>/jquery.min.js"></script>
    <script src="<?php echo $resUK; ?>/js/uikit.min.js"></script>
    <script src="<?php echo $resUK; ?>/js/components/notify.min.js"></script>
    <script src="<?php echo $resUK; ?>/js/components/tooltip.min.js"></script>
    <script src="<?php echo $resUK; ?>/js/components/upload.min.js"></script>
    <script src="<?php echo SITE_BASE_URL; ?>/lib/jquery-ui-autocomplete-1.11.4/jquery-ui.min.js"></script>
    <script src="<?php echo SITE_BASE_URL; ?>/lib/tag-handler-1.3.1/js/jquery.taghandler.min.js"></script>
    <!-- <script src="<?php echo SITE_BASE_URL; ?>/lib/arrowchat/includes/js/jquery-ui.js"></script> -->
    
    <?php if ($GLOBALS['includePicker']) { ?>
    <link rel="stylesheet" href="<?php echo $resSM; ?>/spectrum.css" />
    <script src="<?php echo $resSM; ?>/spectrum.js"></script>
    <?php } ?>
    
    <?php if ($GLOBALS['includeCharts']) { ?>
    <script src="<?php echo $resCJ; ?>/Chart.min.js"></script>
    <?php } ?>
    
    <?php if ($GLOBALS['includeSlides']) { ?>
    <link rel="stylesheet" href="<?php echo $resUK; ?>/css/components/slideshow.almost-flat.min.css" />
    <script src="<?php echo $resUK; ?>/js/components/slideshow.min.js"></script>
    <?php } ?>
    
    <?php if ($GLOBALS['includePlayer']) { ?>
    <link rel="stylesheet" href="<?php echo $resUK; ?>/css/components/slidenav.almost-flat.min.css" />
    <script src="<?php echo SITE_BASE_URL; ?>/lib/uikit-2.26.3/js/components/lightbox.js"></script>
    <?php } ?>
    
    <?php
    // these are stored in separate files because some pages need 
    // to include them after the header has already been sent
    if ($GLOBALS['includeEditor']) { require('header-editor.php'); }
    if ($GLOBALS['includeStripe']) { require('header-stripe.php'); }
    ?>
</head>

<body>

<div class="gbb-header">
    <h1 class="gbb-header-text"><span><?php if (substr($_SESSION['pagerqst'], 0, 1) != 'g') { echo 'GrokBB'; } ?></span></h1>
</div>

<nav class="uk-navbar uk-navbar-attached gbb-navbar gbb-padding">
    <div class="uk-grid uk-grid-small" data-uk-grid-margin>
        <div class="uk-width-large-7-10">
            <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                <div class="uk-button-dropdown uk-dropdown-close" data-uk-dropdown="{ mode: 'click' }">
                    <div class="uk-button-group">
                        <button class="uk-button uk-text-bold gbb-menu">GrokBB Menu</button>
                        <button class="uk-button"><i class="uk-icon-caret-down"></i></button>
                    </div>
                
                    <div class="uk-dropdown uk-dropdown-small">
                        <ul class="uk-nav uk-nav-dropdown">
                            <?php if (isset($_SESSION['user'])) { ?>
                            <li class="uk-hidden-large">
                                <a href="#"><i class="gbb-menu-icon uk-icon-user"></i> <?php echo $_SESSION['user']->username; ?></a>
                                <ul class="uk-nav-sub">
                                    <li class="uk-nav-divider" style="margin-top: 0px"></li>
                                    <li><a href="<?php echo SITE_BASE_URL; ?>/user/profile">My Profile</a></li>
                                    <li><a href="<?php echo SITE_BASE_URL; ?>/user/friends">My Friends</a></li>
                                    <li><a href="<?php echo SITE_BASE_URL; ?>/user/topics">My Topics</a></li>
                                    <li><a href="<?php echo SITE_BASE_URL; ?>/user/replies">My Replies</a></li>
                                    <li class="uk-nav-divider"></li>
                                    <li><a href="<?php echo SITE_BASE_URL; ?>/user/messages" class="<?php echo ($announcement || $inboxMessage) ? 'uk-text-danger' : ''; ?>">My Messages</a></li>
                                    <li><a href="#" data-uk-modal="{ target: '#modal-settings', bgclose: false, center: true }">My Settings</a></li>
                                    <li class="uk-nav-divider"></li>
                                    <li><a href="#" id="logout">Log Out</a></li>
                                    <li class="uk-nav-divider"></li>
                                </ul>
                            </li>
                            <?php } else { ?>
                            <li class="uk-hidden-large"><a href="#" onclick="return false" data-uk-modal="{ target: '#modal-login', bgclose: false, center: true }"><i class="gbb-menu-icon uk-icon-sign-in"></i> GrokBB Login / Sign Up</a></li>
                            <?php } ?>
                            <?php if ($_SESSION['pagerqst']) { ?>
                            <li><a href="<?php echo SITE_BASE_URL; ?>"><i class="gbb-menu-icon uk-icon-home"></i> GrokBB Home</a></li>
                            <?php } ?>
                            <li><a href="<?php echo SITE_BASE_URL; ?>/home/search"><i class="gbb-menu-icon uk-icon-search"></i> GrokBB Search</a></li>
                            <li><a href="<?php echo SITE_BASE_URL; ?>/g/GrokBB_Help"><i class="gbb-menu-icon uk-icon-question-circle"></i> GrokBB Help</a></li>
                            <li><a href="<?php echo SITE_BASE_URL; ?>/g/GrokBB_Dev"><i class="gbb-menu-icon uk-icon-floppy-o"></i> GrokBB Dev</a></li>
                            <?php if ($_SESSION['user']) { ?>
                                <?php if ($myBoards) { ?>
                                    <li class="uk-nav-header gbb-spacing-small">My Boards</li>
                                    <?php
                                    foreach ($myBoards as $myBoard) {
                                        if (\GrokBB\Board::isArchived($myBoard)) {
                                            continue; // the user has lost ownership to their board
                                        }
                                    ?>
                                    <li><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $myBoard->name); ?>"><?php echo $myBoard->name; ?></a></li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($fvBoards) { ?>
                                    <li class="uk-nav-header gbb-spacing-small">My Favorites</li>
                                    <li><a href="<?php echo SITE_BASE_URL; ?>/user/favorites"><i class="gbb-menu-icon uk-icon-clone"></i>View All</a></li>
                                    <?php foreach ($fvBoards as $fvBoard) { ?>
                                    <li><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $fvBoard->name); ?>"><?php echo $fvBoard->name; ?></a></li>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                
                &nbsp;&nbsp;&nbsp;
                
                <div class="uk-vertical-align uk-hidden-xsmall">
                    <div class="uk-vertical-align-middle">
                    <?php
                    // include the common header
                    $sepPos = strrpos($_SESSION['template'], DIRECTORY_SEPARATOR) + 1;
                    $dotPos = strpos($_SESSION['template'], '.');
                    
                    $header = substr($_SESSION['template'], 0, $sepPos) . 'header' . substr($_SESSION['template'], $dotPos);
                    if (is_readable($header)) { require($header); }
                    
                    ob_end_flush();
                    ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="uk-hidden-medium uk-hidden-small uk-width-large-3-10">
            <div class="uk-navbar-flip">
                <?php if ($_SESSION['user']) { ?>
                <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                    <div class="uk-button-group" data-uk-tooltip="{ pos: 'top-right' }" title="Manage Your Identity">
                        <button class="uk-button uk-text-bold"><?php echo $_SESSION['user']->username; ?></button>
                        <button class="uk-button"><i class="uk-icon-caret-down"></i></button>
                    </div>
                    <div class="uk-dropdown uk-dropdown-small">
                        <ul class="uk-nav uk-nav-dropdown">
                            <li><a href="<?php echo SITE_BASE_URL; ?>/user/profile"><i class="gbb-menu-icon gbb-menu-icon-left uk-icon-user"></i> My Profile</a></li>
                            <li><a href="<?php echo SITE_BASE_URL; ?>/user/friends"><i class="gbb-menu-icon uk-icon-users"></i> <span class="gbb-menu-icon-left">My Friends</span></a></li>
                            <li><a href="<?php echo SITE_BASE_URL; ?>/user/topics"><i class="gbb-menu-icon gbb-menu-icon-left uk-icon-file"></i> My Topics</a></li>
                            <li><a href="<?php echo SITE_BASE_URL; ?>/user/replies"><i class="gbb-menu-icon gbb-menu-icon-left uk-icon-comments"></i> My Replies</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="uk-button-group">
                    <a href="<?php echo SITE_BASE_URL; ?>/user/messages" class="uk-button" data-uk-tooltip="{ pos: 'top-right' }" title="Read / Send Messages"><i class="uk-icon-envelope <?php echo ($announcement || $inboxMessage) ? 'uk-text-danger' : ''; ?>"></i></a>
                    <a href="#" class="uk-button" data-uk-modal="{ target: '#modal-settings', bgclose: false, center: true }" data-uk-tooltip="{ pos: 'top-right' }" title="Update Your Email or Password"><i class="uk-icon-cog"></i></a>
                </div>
                
                &nbsp;&nbsp;
                
                <div class="uk-button-group">
                    <a href="#" id="logout" class="uk-button" data-uk-tooltip="{ pos: 'top-right' }" title="Log Out"><i class="uk-icon-power-off"></i></a>
                </div>
                <?php } else if ($GLOBALS['db']) { ?>
                <button class="uk-button uk-hidden-small" data-uk-modal="{ target: '#modal-login', bgclose: false, center: true }"><span class="uk-text-bold">Login or Sign Up</span><span class="uk-hidden-medium"> in seconds</span> <i class="uk-icon-sign-in"></i></button>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<div class="gbb-padding">