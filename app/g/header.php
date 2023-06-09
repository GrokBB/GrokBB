<?php
if (empty($_SESSION['gbbboard'])) {
    header('Location: ' . SITE_BASE_URL . '/error/template');
}

$nameCleaned = GrokBB\Util::sanitizeBoard($_SESSION['gbbboard']);
$board = $GLOBALS['db']->getOne('board b INNER JOIN ' . DB_PREFIX . 'board_settings bs ON b.id = bs.id_bd LEFT JOIN ' . DB_PREFIX . 'user_board ub ON b.id = ub.id_bd AND ub.deleted = 0 AND ub.id_ur = ' . ((isset($_SESSION['user'])) ? $_SESSION['user']->id : 0), 
    array('b.name' => $nameCleaned), false, array('b.*', 'bs.*', 'b.id as id', 'bs.id as bsid', 'ub.added as favorite'));

if ($board === false) {
    header('Location: ' . SITE_BASE_URL . '/error/template');
}

$board->isArchived = \GrokBB\Board::isArchived($board);

if (isset($_SESSION['user'])) {
    $_SESSION['user']->isOwner = ($_SESSION['user']->isAdmin) ? $board->id : false;
    $_SESSION['user']->isModerator = false;
    $_SESSION['user']->isBanned = false;
}

if (isset($_SESSION['user']) && $board->id_ur == $_SESSION['user']->id && $board->isArchived == false) {
    $_SESSION['user']->isOwner = $board->id;
}

if (\GrokBB\Board::isBanned($board->id)) {
    $_SESSION['user']->isBanned = true;
    $GLOBALS['displayHeader'] = false;
} else if (\GrokBB\Board::isModerator($board->id)) {
    $_SESSION['user']->isModerator = $board->id;
}

if (in_array($_SESSION['pagerqst'], array('g/settings')) && $_SESSION['user']->isOwner == false) {
    header('Location: ' . SITE_BASE_URL);
}

if (in_array($_SESSION['pagerqst'], array('g/users')) && $_SESSION['user']->isModerator == false) {
    header('Location: ' . SITE_BASE_URL);
}

if (in_array($_SESSION['pagerqst'], array('g/topic')) && $board->isArchived) {
    header('Location: ' . SITE_BASE_URL . '/g/' . $_SESSION['gbbboard']);
}

if (in_array($_SESSION['pagerqst'], array('g/topic', 'g/view')) && $_SESSION['user']->isBanned) {
    header('Location: ' . SITE_BASE_URL . '/g/' . $_SESSION['gbbboard']);
}

$_SESSION['board'] = GrokBB\Util::sanitizeSession($board);
$_SESSION['sort']  = '';

if ($GLOBALS['displayHeader'] === false) { return; }
if ($board->type == 1 && \GrokBB\Board::isApproved($board->id) == false) { return; }

$categories = $GLOBALS['db']->getAll('board_category', array('id_bd' => $board->id), 'defcat DESC, name ASC');

// get all the tags used by the logged in user or added by a moderator
$tags = $GLOBALS['db']->custom('SELECT tt.* FROM ' . DB_PREFIX . 'topic_tag tt INNER JOIN ' . DB_PREFIX . 'topic t ON tt.id_tc = t.id ' .
    'WHERE t.id_bd = ' . $board->id . ' AND (tt.id_bd = ' . $board->id . ((isset($_SESSION['user']) && $_SESSION['user']->id) ? ' OR tt.id_ur = ' . $_SESSION['user']->id : '') . ') ORDER BY tt.name, tt.id');

$tagsByTopic = array();
$tagsModerator = array();
$tagsAvailable = array();

$tagsForUR = array();
$tagsForBD = array();

foreach ($tags as $tag) {
    $tagsByTopic[$tag->id_tc][] = $tag->name;
    
    if ($tag->id_bd > 0 && (!isset($_SESSION['user']) || $_SESSION['user']->isModerator == false)) {
        $tagsModerator[$tag->id_tc][$tag->name] = 1;
    }
    
    if (isset($_SESSION['user']) && !in_array($tag->name, $tagsAvailable)) {
        if ($tag->id_ur == $_SESSION['user']->id || ($_SESSION['user']->isModerator == $board->id && $tag->id_bd == $board->id)) {
            $tagsAvailable[] = $tag->name;
        }
    }
    
    if (isset($_SESSION['user']) && $tag->id_ur == $_SESSION['user']->id && !in_array($tag->name, array_keys($tagsForUR))) {
        $tagsForUR[$tag->name] = $tag;
    } else if ($tag->id_bd == $board->id && !in_array($tag->name, array_keys($tagsForBD))) {
        $tagsForBD[$tag->name] = $tag;
    }
}

$_SESSION['tagsByTopic'] = $tagsByTopic;
$_SESSION['tagsModerator'] = $tagsModerator;
$_SESSION['tagsAvailable'] = $tagsAvailable;

$queryURL = SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/search';

if (isset($_COOKIE['board_prefs'])) {
    $bp = json_decode($_COOKIE['board_prefs'], true);
    
    $sort = (isset($bp[$board->id]['sort'])) ? $bp[$board->id]['sort'] : '';
    if (!in_array($sort, array('pop', 'new', 'qry'))) { $sort = 'pop'; }
    
    if ($sort == 'qry') {
        $sqry['text'] = GrokBB\Util::sanitizeTXT($bp[$board->id]['sqry']['text']);
        
        $sqryType = $bp[$board->id]['sqry']['type'];
        if (!in_array($sqryType, array('title', 'content', 'both'))) { $sqryType = 'both'; }
        $sqry['type'] = $sqryType;
        
        $sqry['user'] = GrokBB\Util::sanitizeTXT($bp[$board->id]['sqry']['user']);
        
        $sqrySort = $bp[$board->id]['sqry']['sort'];
        if (!in_array($sqrySort, array('rel', 'pop', 'new', 'old', 'usr', 'tit', 'cat'))) { $sqrySort = 'rel'; }
        $sqry['sort'] = $sqrySort;
    }
    
    if (isset($bp[$board->id]['bcat'])) {
        $bcat = preg_replace('/[^0-9\-]/', '', $bp[$board->id]['bcat']);
        $bcat = ($bcat) ? explode('-', $bcat) : array();
    } else {
        $bcat = array();
    }
    
    $btagUR = array();
    $btagTypeUR = 'Any';
    $btagBD = array();
    $btagTypeBD = 'Any';
    
    if (isset($bp[$board->id]['btag'])) {
        $btag = preg_replace('/[^0-9\-ubAnyl]/', '', $bp[$board->id]['btag']);
        $btag = ($btag) ? explode('-', $btag) : array();
        
        foreach ($btag as $id) {
            if (substr($id, 0, 1) == 'u') {
                $id = substr($id, 1);
                
                if ($id == 'Any' || $id == 'All') {
                    $btagTypeUR = $id;
                } else {
                    $btagUR[] = $id;
                }
            } else if (substr($id, 0, 1) == 'b') {
                $id = substr($id, 1);
                
                if ($id == 'Any' || $id == 'All') {
                    $btagTypeBD = $id;
                } else {
                    $btagBD[] = $id;
                }
            }
        }
    } else {
        $btag = array();
    }
    
    $rply = (isset($bp[$board->id]['rply'])) ? $bp[$board->id]['rply'] : '';
    if (!in_array($rply, array('old', 'new', 'res', 'sav'))) { $rply = 'old'; }
} else {
    $sort = 'new';
    $bcat = array();
    $btag = array();
    $rply = 'old';
}

$_SESSION['sort'] = $sort;

// display the custom board headers
switch ($_SESSION['pagerqst']) {
    case 'g/view' : case 'g/topic' : case 'g/settings' : case 'g/users' :
        ?><span class="gbb-icon-large view-back"><i class="uk-icon-sign-out gbb-icon-flip-h view-back-icon"></i>&nbsp;&nbsp;<a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard']; ?>" class="view-back-text">Back to Topics</a></span><?php
        return;
}
?>

<div id="header-search" class="uk-button-group" data-uk-button-radio>
    <a class="uk-button <?php echo ($sort == 'pop') ? 'uk-button-primary' : ''; ?> gbb-button-static" type="button" href="<?php echo $queryURL; ?>/sort/pop">Popular</a>
    <a class="uk-button <?php echo ($sort == 'new') ? 'uk-button-primary' : ''; ?> gbb-button-static" type="button" href="<?php echo $queryURL; ?>/sort/new">New</a>
    <div class="uk-button <?php echo ($sort == 'qry') ? 'uk-button-primary' : ''; ?> gbb-button-static" type="button" id="header-search-qry"><i class="uk-icon-search"></i></div>
</div>

&nbsp;&nbsp;

<div class="uk-button-group">
    <a class="uk-button <?php echo ($bcat) ? 'uk-button-primary' : ''; ?> gbb-button-static" type="button" <?php echo (($bcat) ?  'href="' . $queryURL . '/bcat/0"' : 'data-uk-modal="{ target: \'#modal-configure-categories\', bgclose: false, center: true }"'); ?> data-uk-tooltip="{ pos: 'top-left' }" title="<?php echo ($bcat) ? 'Remove Category Filter' : 'Filter Topics By Category'; ?>">By Category</a>
    <a href="#" class="uk-button gbb-button-static" data-uk-modal="{ target: '#modal-configure-categories', bgclose: false, center: true }" data-uk-tooltip="{ pos: 'top-right' }" title="Configure Categories"><i class="uk-icon-cog"></i></a>
</div>

&nbsp;&nbsp;

<div class="uk-button-group">
    <a class="uk-button <?php echo ($btag) ? 'uk-button-primary' : ''; ?> gbb-button-static" type="button" <?php echo (($btag) ?  'href="' . $queryURL . '/btag/0"' : 'data-uk-modal="{ target: \'#modal-configure-tags\', bgclose: false, center: true }"'); ?> data-uk-tooltip="{ pos: 'top-left' }" title="<?php echo ($btag) ? 'Remove Tag Filter' : 'Filter Topics By Tags'; ?>">By Tags</a>
    <a href="#" class="uk-button gbb-button-static" data-uk-modal="{ target: '#modal-configure-tags', bgclose: false, center: true }" data-uk-tooltip="{ pos: 'top-right' }" title="Configure Tags"><i class="uk-icon-cog"></i></a>
</div>

<div id="header-subnav" class="uk-grid uk-padding-remove gbb-spacing-large gbb-padding-large" style="display: none">
    <div class="uk-width-1-1">
        <div class="uk-panel uk-panel-box uk-panel-secondary">
            <h3 class="uk-panel-title uk-text-primary uk-text-bold" data-uk-modal="{ target: '#modal-search-guide', bgclose: false, center: true }" style="cursor: pointer"><span data-uk-tooltip="{ pos: 'right' }" title="Click to View Search Guide">Custom Search&nbsp;&nbsp;<i class="uk-icon-question-circle gbb-help-icon"></i>&nbsp;</span></h3>
            <div class="uk-form uk-grid uk-grid-divider">
                <div class="uk-width-1-3">
                    <input type="text" id="qry-type-text" class="uk-form-width-large" placeholder="Search for this text in the ..." value="<?php echo ($sort == 'qry') ? str_replace('"', '&quot;', $sqry['text']) : ''; ?>">
                    <br><br>
                    
                    <input type="radio" name="qry-type" value="title" <?php echo ($sort == 'qry' && $sqry['type'] == 'title') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Title
                    &nbsp;&nbsp;
                    <input type="radio" name="qry-type" value="content" <?php echo ($sort == 'qry' && $sqry['type'] == 'content') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Content
                    &nbsp;&nbsp;
                    <input type="radio" name="qry-type" value="both" <?php echo ($sort != 'qry' || $sqry['type'] == 'both') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Title & Content
                </div>
                <div class="uk-width-1-3">
                    <fieldset>
                        <legend>Created By</legend>
                        
                        <input type="radio" name="qry-user" value="" <?php echo ($sort != 'qry' || $sqry['user'] != 'moderator') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Specific User
                        &nbsp;&nbsp;
                        <input type="text" id="qry-user-selected" class="uk-form-width-medium" maxlength="15" placeholder="No User Selected" value="<?php echo ($sort == 'qry' && $sqry['user'] != 'moderator') ? $sqry['user'] : ''; ?>">
                        &nbsp;&nbsp;
                        <input type="radio" name="qry-user" value="moderator" <?php echo ($sort == 'qry' && $sqry['user'] == 'moderator') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Moderator
                    </fieldset>
                </div>
                <div class="uk-width-1-3">
                    <fieldset>
                        <legend>Sort By</legend>
                        
                        <table cellspacing="0" cellpadding="5">
                        <tr>
                            <td><input type="radio" name="qry-sort" value="rel" <?php echo ($sort != 'qry' || $sqry['sort'] == 'rel') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Relevance</td>
                            <td><input type="radio" name="qry-sort" value="pop" <?php echo ($sort == 'qry' && $sqry['sort'] == 'pop') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Popular</td>
                            <td><input type="radio" name="qry-sort" value="new" <?php echo ($sort == 'qry' && $sqry['sort'] == 'new') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Newest First</td>
                            <td><input type="radio" name="qry-sort" value="old" <?php echo ($sort == 'qry' && $sqry['sort'] == 'old') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Oldest First</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="qry-sort" value="usr" <?php echo ($sort == 'qry' && $sqry['sort'] == 'usr') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Username</td>
                            <td><input type="radio" name="qry-sort" value="tit" <?php echo ($sort == 'qry' && $sqry['sort'] == 'tit') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Title</td>
                            <td><input type="radio" name="qry-sort" value="cat" <?php echo ($sort == 'qry' && $sqry['sort'] == 'cat') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Category</td>
                        </tr>
                        </table>
                    </fieldset>
                </div>
                
                <br />
                
                <div class="uk-width-1-1">
                    <button class="uk-button uk-button-primary uk-align-right gbb-spacing" type="button" id="header-search-qry-submit">Search Topics</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-configure-categories" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Filter Topics by Category</div>
                
                <div class="uk-grid gbb-padding-large">
                    <?php foreach ($categories as $category) { ?>
                    <div class="uk-width-small-1-2 uk-width-large-1-3 uk-width-xlarge-1-4">
                        <table cellpadding="5" cellspacing="0">
                        <tr>
                            <td><input id="category-<?php echo $category->id; ?>" name="category[]" value="<?php echo $category->id; ?>" type="checkbox" <?php echo (in_array($category->id, $bcat)) ? 'checked="checked"' : ''; ?> /></td>
                            <td id="category-box-<?php echo $category->id; ?>">
                                <?php if ($category->image) { ?>
                                <img src="<?php echo SITE_BASE_URL . '/img.php?bid=' . $board->id . '&cat=' . $category->id; ?>" width="200" height="40" data-uk-tooltip="{ pos: 'top-right' }" title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>">
                                <?php } else { ?>
                                <figure class="uk-overlay" style="background-color: <?php echo $category->color; ?>">
                                    <img src="<?php echo SITE_BASE_URL . '/img/category.png'; ?>" width="200" height="40" title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>">
                                    <figcaption class="uk-overlay-panel uk-flex uk-flex-middle"><?php echo $category->name; ?></figcaption>
                                </figure>
                                <?php } ?>
                            </td>
                        </tr>
                        </table>
                    </div>
                    <?php } ?>
                </div>
                
                <div class="uk-width-1-1 uk-text-right gbb-padding">
                    <a id="configure-categories-apply" class="uk-button uk-button-primary">Apply Filter</a>
                    &nbsp;
                    <a class="uk-button uk-button-primary uk-modal-close">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TODO: detect when new tags have been added and display a buton to reload the page -->

<div id="modal-configure-tags" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Filter Topics by Tags</div>
                
                <div class="uk-form-row">
                    <div class="uk-panel uk-panel-box uk-panel-box-secondary uk-panel-header">
                        <div class="uk-panel-badge uk-badge uk-badge-success"><?php echo count($tagsForUR); ?></div>
                        <div class="uk-panel-title">
                            <h4 class="uk-text-bold uk-text-primary uk-margin-remove">Your Tags</h4>
                            <div class="uk-text-small gbb-spacing">These are the tags you have added to topics in this board.</div>
                            <div class="uk-text-small">Show topics that contain <input type="radio" name="uType" value="Any" <?php echo (empty($btag) || $btagTypeUR == 'Any') ? 'checked="checked"' : ''; ?> />&nbsp;ANY&nbsp;&nbsp;or&nbsp;<input type="radio" name="uType" value="All" <?php echo ($btag && $btagTypeUR == 'All') ? 'checked="checked"' : ''; ?> />&nbsp;ALL&nbsp;&nbsp;of the selected tags.</div>
                        </div>
                        
                        <div id="tag-list-u" class="tag-list">
                            <?php if ($tagsForUR) { ?>
                            <ul class="uk-grid uk-grid-collapse" data-uk-grid-margin>
                                <?php foreach ($tagsForUR as $tag) { ?>
                                <li class="uk-width">
                                    <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td><input id="tag-<?php echo $tag->id; ?>" name="tag[]" value="<?php echo $tag->id; ?>" type="checkbox" <?php echo (in_array('u' . $tag->id, $btag)) ? 'checked="checked"' : ''; ?> /></td>
                                        <td id="tag-box-<?php echo $tag->id; ?>">
                                            <div class="tagHandler"><ul class="tagHandlerContainer" style="border: none"><li class="tagItem"><?php echo $tag->name; ?></li></ul></div>
                                        </td>
                                    </tr>
                                    </table>
                                </li>
                                <?php } ?>
                            </ul>
                            <?php } else { ?>
                            <p class="uk-alert uk-alert-info uk-text-small tag-none">
                                You have not tagged any topics.
                            </p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                
                <div class="uk-form-row">
                    <div class="uk-panel uk-panel-box uk-panel-box-secondary uk-panel-header">
                        <div class="uk-panel-badge uk-badge uk-badge-success"><?php echo count($tagsForBD); ?></div>
                        <div class="uk-panel-title">
                            <h4 class="uk-text-bold uk-text-primary uk-margin-remove"><?php echo $board->name; ?> Tags</h4>
                            <div class="uk-text-small gbb-spacing">These are the tags moderators have added to topics in this board.</div>
                            <div class="uk-text-small">Show topics that contain <input type="radio" name="bType" value="Any" checked="checked" <?php echo (empty($btag) || $btagTypeBD == 'Any') ? 'checked="checked"' : ''; ?> />&nbsp;ANY&nbsp;&nbsp;or&nbsp;<input type="radio" name="bType" value="All" <?php echo ($btag && $btagTypeBD == 'All') ? 'checked="checked"' : ''; ?> />&nbsp;ALL&nbsp;&nbsp;of the selected tags.</div>
                        </div>
                        
                        <div id="tag-list-b" class="tag-list">
                            <?php if ($tagsForBD) { ?>
                            <ul class="uk-grid uk-grid-collapse" data-uk-grid-margin>
                                <?php foreach ($tagsForBD as $tag) { ?>
                                <li class="uk-width">
                                    <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td><input id="tag-<?php echo $tag->id; ?>" name="tagBoard[]" value="<?php echo $tag->id; ?>" type="checkbox" <?php echo (in_array('b' . $tag->id, $btag)) ? 'checked="checked"' : ''; ?> /></td>
                                        <td id="tag-box-<?php echo $tag->id; ?>">
                                            <div class="tagHandler"><ul class="tagHandlerContainer" style="border: none"><li class="tagItem"><?php echo $tag->name; ?></li></ul></div>
                                        </td>
                                    </tr>
                                    </table>
                                </li>
                                <?php } ?>
                            </ul>
                            <?php } else { ?>
                            <p class="uk-alert uk-alert-info uk-text-small tag-none">
                                The moderators have not tagged any topics.
                            </p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                
                <div class="uk-width-1-1 uk-text-right gbb-spacing-large">
                    <a id="configure-tags-apply" class="uk-button uk-button-primary">Apply Filter</a>
                    &nbsp;
                    <a class="uk-button uk-button-primary uk-modal-close">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>