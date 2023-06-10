<?php
$GLOBALS['includeEditor'] = true;

require(SITE_BASE_APP . 'header.php');

$maximum = 500;
$perPage = 50;
$numPage = (isset($_GET['page']) && $_GET['page'] > 0) ? (int) $_GET['page'] : 1;

$orderBy = (isset($sort) && $sort == 'qry') ? $sqry['sort'] : $sort;

switch ($orderBy) {
    case 'rel' :
        $sortSQL = false;
        break;
    case 'pop' :
        $calc = '((t.%col% / (SELECT MAX(%col%) + 1 FROM ' . DB_PREFIX . 'topic WHERE id_bd = ' . $board->id . ')) * 100)';
        
        // a topic's views are weighted at 40% and replies at 60%
        $wgts = array('counter_views' => '0.40', 'counter_replies' => '0.60');
        
        $sortSQL = '(';
        
        foreach ($wgts as $wgtCol => $wgtVal) {
            $sortSQL .= '(' . str_replace('%col%', $wgtCol, $calc) . ' * ' . $wgtVal . ') + ';
        }
        
        $sortSQL = substr($sortSQL, 0, -3) . ') DESC';
        
        break;
    case 'new' :
    case 'old' :
        if ($orderBy == 'new') {
            $sortSQL = 'IF (r.id, MAX(r.created), t.created) DESC';
        } else {
            $sortSQL = 't.created ASC';
        }
        
        break;
    case 'qry' :
        $sortSQL = 't.created DESC';
        break;
    case 'usr' :
        $sortSQL = 'u.username ASC, t.created DESC';
        break;
    case 'tit' :
        $sortSQL = 't.title ASC, t.created DESC';
        break;
    case 'cat' :
        $sortSQL = 'bc.name ASC, t.created DESC';
        break;
    default:
        $sortSQL = false;
}

$filter = array('t.id_bd' => $board->id, 't.deleted' => 0);
if (!empty($bcat)) { $filter['t.id_bc'] = array('IN', $bcat); }

$urTagsSQL = '';
$bdTagsSQL = '';

if (!empty($btag)) {
    $urTags = array();
    $bdTags = array();
    
    if (!empty($btagUR) && isset($_SESSION['user'])) {
        $urTagsById = $GLOBALS['db']->getAll('topic_tag', array('id' => array('IN', $btagUR)));
        
        if ($urTagsById) {
            $urTagsNum = 0;
            
            foreach ($urTagsById as $tag) {
                $urTagsNum++;
                
                if ($btagTypeUR == 'All') {
                    $urTagsSQL .= ' INNER JOIN ' . DB_PREFIX . 'topic_tag ttUR' . $urTagsNum . ' ON t.id = ttUR' . $urTagsNum . '.id_tc';
                
                    $filter['ttUR' . $urTagsNum . '.name'] = $tag->name;
                    $filter['ttUR' . $urTagsNum . '.id_ur'] = $_SESSION['user']->id;
                } else {
                    $urTags[] = $tag->name;
                }
            }
            
            if ($btagTypeUR != 'All') {
                $urTagsSQL = ' INNER JOIN ' . DB_PREFIX . 'topic_tag ttUR ON t.id = ttUR.id_tc';
                
                $filter['ttUR.name'] = array('IN', $urTags);
                $filter['ttUR.id_ur'] = $_SESSION['user']->id;
            }
        }
    }
    
    if (!empty($btagBD)) {
        $bdTagsById = $GLOBALS['db']->getAll('board_tag bt INNER JOIN ' . DB_PREFIX . 'topic_tag tt ON bt.name = tt.name', 
            array('tt.id_bd' => $board->id, 'tt.id' => array('IN', $btagBD)));
        
        if ($bdTagsById) {
            $bdTagsNum = 0;
            
            foreach ($bdTagsById as $tag) {
                $bdTagsNum++;
                
                if ($btagTypeBD == 'All') {
                    $bdTagsSQL .= ' INNER JOIN ' . DB_PREFIX . 'topic_tag ttBD' . $bdTagsNum . ' ON t.id = ttBD' . $bdTagsNum . '.id_tc';
                
                    $filter['ttBD' . $bdTagsNum . '.name'] = $tag->name;
                    $filter['ttBD' . $bdTagsNum . '.id_bd'] = $board->id;
                } else {
                    $bdTags[] = $tag->name;
                }
                
            }
            
            if ($btagTypeBD != 'All') {
                $bdTagsSQL = ' INNER JOIN ' . DB_PREFIX . 'topic_tag ttBD ON t.id = ttBD.id_tc';
                
                $filter['ttBD.name'] = array('IN', $bdTags);
                $filter['ttBD.id_bd'] = $board->id;
            }
        }
    }
}

if (isset($sort) && $sort == 'qry') {
    if (trim($sqry['text']) != '') {
        switch ($sqry['type']) {
            case 'title' :
                $matchColumns = 't.title';
                break;
            case 'content' :
                $matchColumns = 't.content';
                break;
            case 'both' : default :
                $matchColumns = 't.title, t.content';
                break;
        }
        
        $filter[$matchColumns] = array('MATCH', $sqry['text']);
    }
    
    if (trim($sqry['user']) != '') {
        if ($sqry['user'] == 'moderator') {
            $filter['ubm.id,owner.id'] = array('IS NOT NULL');
        } else {
            $filter['u.username'] = $sqry['user'];
        }
    }
} else {
    $filterSticky = array('t.id_bd' => $board->id, 't.deleted' => 0, 't.sticky' => array('>', 0));
    
    if (isset($_SESSION['user']) == false || $_SESSION['user']->isModerator == false) {
        $filterSticky['t.private'] = 0;
    }
    
    $sticky = $GLOBALS['db']->getAll('topic t INNER JOIN ' . DB_PREFIX . 'user u ON t.id_ur = u.id INNER JOIN ' . DB_PREFIX . 'board_category bc ON t.id_bc = bc.id' . (($orderBy == 'new') ? ' LEFT JOIN ' . DB_PREFIX . 'reply r ON t.id = r.id_tc' : ''), 
        $filterSticky, 't.created', array('DISTINCT(t.id) as tid', 't.*', 'u.*', 'u.id as uid', 't.created as created', 't.updated as updated', 't.counter_replies as counter_replies', 'bc.id as bcid', 'bc.name as bcname', 'bc.color as bccolor', 'bc.image as bcimage'), $maximum, false, (($orderBy == 'new') ? 't.id' : ''));
    
    $filter['t.sticky'] = 0;
}

if (isset($_SESSION['user']) == false || $_SESSION['user']->isModerator == false) {
    $filter['t.private'] = 0;
}

$topics = $GLOBALS['db']->getAll('topic t INNER JOIN ' . DB_PREFIX . 'user u ON t.id_ur = u.id INNER JOIN ' . DB_PREFIX . 'board_category bc ON t.id_bc = bc.id' . $urTagsSQL . $bdTagsSQL . (($orderBy == 'new') ? ' LEFT JOIN ' . DB_PREFIX . 'reply r ON t.id = r.id_tc' : '') . 
    ((isset($sqry['user']) && $sqry['user'] == 'moderator') ? ' LEFT JOIN ' . DB_PREFIX . 'user_board_moderator ubm ON u.id = ubm.id_ur AND ubm.id_bd = ' . $board->id . ' LEFT JOIN ' . DB_PREFIX . 'user owner ON u.id = owner.id AND owner.id = ' . $board->id_ur : ''), 
    $filter, $sortSQL, array('DISTINCT(t.id) as tid', 't.*', 'u.*', 'u.id as uid', 't.created as created', 't.updated as updated', 't.counter_replies as counter_replies', 'bc.id as bcid', 'bc.name as bcname', 'bc.color as bccolor', 'bc.image as bcimage'), $maximum, false, (($orderBy == 'new') ? 't.id' : ''));

if ($topics) {
    $topicsByPage = array_chunk($topics, $perPage);
    $topicsByPageCount = count($topicsByPage);
    $topics = $topicsByPage[$numPage - 1];
} else {
    $topicsByPage = array();
    $topicsByPageCount = 0;
    $topics = array();
}

if (isset($sticky) && $sticky) {
    if ($topics) {
        $topics = array_merge($sticky, $topics);
    } else {
        $topics = $sticky;
    }
}

$tSaved = array();
$tUsers = array();
$bUsers = array();

$_SESSION['topics'] = array();

if ($topics) {
    foreach ($topics as $topic) {
        $_SESSION['topics'][] = $topic->tid;
        
        $tUsers[] = $topic->id_ur;
        
        if (isset($_SESSION['user']) && $topic->id_ur == $_SESSION['user']->id) {
            $tSaved[] = $topic->tid;
        }
    }
    
    $newReplies = GrokBB\Topic::newReplies($_SESSION['topics']);
    
    $topicMedia = $GLOBALS['db']->getAll('topic_media', array('id_tc' => array('IN', $_SESSION['topics'])), 'id');
    $topicMediaFirst = array();
    
    if ($topicMedia) {
        foreach ($topicMedia as $tm) {
            if (!in_array($tm->id_tc, array_keys($topicMediaFirst))) {
                $topicMediaFirst[$tm->id_tc] = $tm;
            }
        }
    }
    
    $tUsers = array_keys(array_flip($tUsers));
    
    $uStats = new stdClass();
    $uStats->board = array();
    $uStats->users = array();
    
    $statistics = $GLOBALS['db']->getAll('user_board_stats', array('id_bd' => $board->id, 'id_ur' => array('IN', $tUsers)));
    foreach ($statistics as $stats) { $uStats->board[$stats->id_ur] = $stats; }
    
    $statistics = $GLOBALS['db']->getAll('user', array('id' => array('IN', $tUsers)), false, array('id', 'counter_topics', 'counter_replies'));
    foreach ($statistics as $stats) { $uStats->users[$stats->id] = $stats; }
    
    $badges = $GLOBALS['db']->getAll('user_board_badge ubb LEFT JOIN ' . DB_PREFIX . 'board_badge bb ON ubb.id_bb = bb.id', array('bb.id_bd' => $board->id, 'ubb.id_ur' => array('IN', $tUsers)), false, array('ubb.*', 'COUNT(ubb.id_ur) as counter_badges'), false, false, 'ubb.id_ur');
    foreach ($badges as $badge) { $bUsers[$badge->id_ur] = $badge->counter_badges; }
}

if (isset($_SESSION['user'])) {
    $tSavedResults = $GLOBALS['db']->getAll('user_topic', array('id_ur' => $_SESSION['user']->id));
    foreach ($tSavedResults as $tsr) { $tSaved[] = $tsr->id_tc; }
}

if (isset($_SESSION['user']) && $_SESSION['user']->isBanned) {
    $board->board_request_access = 'You are currently banned from this board. Please enter your reason for requesting access below.';
} else if ($board->board_request_access == '') {
    $board->board_request_access = 'This is a private board, and only approved users are allowed to read and post topics. Please enter your reason for requesting access below.';
}

if ($board->isArchived) {
    require(SITE_BASE_APP . 'header-stripe.php');
}
?>

<div class="uk-grid uk-grid-small" data-uk-grid-margin>
    <div class="uk-width-small-1-1 uk-width-large-3-4">
        <?php if (($board->type == 1 && \GrokBB\Board::isApproved($board->id) == false) || (isset($_SESSION['user']) && $_SESSION['user']->isBanned)) { ?>
        <div id="request-frm" class="uk-panel uk-panel-box uk-panel-header">
            <div class="uk-panel-title">
                <span class="uk-text-bold uk-text-primary">Request For Access</span>
                <button id="request-access-submit" class="uk-button uk-button-primary uk-align-right gbb-editor-button">Submit Request</button>
                <div class="uk-text-small uk-align-right gbb-editor-characters"><span id="editor-request-access-char">0</span> / 15,000 characters</div>
            </div>
            
            <div><?php echo $board->board_request_access; ?></div><br />
            
            <textarea id="editor-request-access"></textarea>
        </div>
        <div id="request-msg" class="uk-alert uk-alert-info uk-margin-remove">
            Your request has been submitted.
        </div>
        <?php } else { ?>
        
        <?php if ($board->isArchived) { ?>
        <input type="hidden" id="board-id" value="<?php echo $board->id; ?>">
        
        <div class="uk-margin-bottom">
            <div id="archive-toggle" class="uk-alert uk-alert-danger uk-text-bold uk-margin-bottom-remove" data-uk-toggle="{ target: '#archive-detail' }" data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to View Details">
                <i class="uk-icon uk-icon-database"></i>&nbsp;&nbsp;Archive Mode is Enabled
            </div>
            <div id="archive-detail" class="uk-hidden">
                <div class="uk-width-1 uk-text-center">
                    <i class="uk-icon uk-icon-caret-down"></i>
                    <i class="uk-icon uk-icon-caret-down"></i>
                    <i class="uk-icon uk-icon-caret-down"></i>
                    <i class="uk-icon uk-icon-caret-down"></i>
                </div>
                <?php if ($board->type == 1) { ?>
                <div id="archive-detail-text" class="uk-alert uk-alert-danger uk-margin-top-remove uk-margin-bottom-remove">
                    The previous owner has cancelled their subscription, and so this board is now archived. This means no new topics or replies are allowed.
                </div>
                <?php } else { ?>
                <div id="archive-detail-text" class="uk-alert uk-alert-danger uk-margin-top-remove uk-margin-bottom-remove">
                    The previous owner has cancelled their subscription, and so this board is now archived. This means no new topics or replies are allowed.<br />
                    If you would like to enable the discussions again, and become the new owner, you can subscribe to one of the following plans.
                    
                    <br /><br />
                    
                    Please note there is no trial period given for archived boards, and your credit card will be charged immediately.
                    
                    <br /><br />
                    
                    <div class="uk-grid">
                        <div class="uk-width-1-4 uk-container-center">
                            <div id="archive-plan-monthly" class="uk-panel uk-panel-box uk-panel-header uk-panel-box-primary">
                                <input type="radio" id="archive-plan-monthly-radio" name="archive-plan" value="1">
                                &nbsp;Monthly ($3 per month)
                            </div>
                        </div>
                        <div class="uk-width-1-4 uk-container-center">
                            <div id="archive-plan-yearly" class="uk-panel uk-panel-box uk-panel-header uk-panel-box-primary">
                                <input type="radio" id="archive-plan-yearly-radio" name="archive-plan" value="0" checked="checked">
                                &nbsp;Yearly ($30 per year)
                            </div>
                        </div>
                        <div class="uk-width-1-4 uk-container-center gbb-spacing">
                            <button id="archive-plan-stripe" class="uk-button uk-button-primary">Subscribe & Enable Discussions</button>
                        </div>
                        <div id="archive-msg-div" class="uk-width-1-4 uk-container-center gbb-spacing">
                            <div id="archive-msg" class="gbb-spacing-small"></div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        
        <div class="uk-hidden-large">
            <div class="uk-panel uk-panel-box">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr><td align="center">
                    
                    <span class="uk-hidden-xsmall">
                        <?php if (isset($_SESSION['user']) && ($_SESSION['user']->isOwner == $board->id || $_SESSION['user']->isModerator == $board->id)) { ?>
                        <?php if ($_SESSION['user']->isOwner == $board->id) { ?>
                        <a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings" class="uk-button uk-button-primary"><span class="uk-hidden-small">My </span>Board Settings</a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users" class="uk-button uk-button-primary">Users & Stats</a>
                        <?php } else { ?>
                        <a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users" class="uk-button uk-button-primary sidebar-button">Moderate Users</a>
                        <?php } ?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <?php } ?>
                        <?php if ($board->type != 1) { ?><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/rss" class="uk-button uk-button-primary"><span class="uk-hidden-small">RSS</span><span class="uk-visible-small"><i class="uk-icon uk-icon-rss"></i></span></a><?php } ?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                    </span>
                    
                    <button <?php echo (isset($_SESSION['user'])) ? 'id="sidebar-favorite"' : 'data-uk-modal="{ target: \'#modal-login\', bgclose: false, center: true }"'; ?> class="uk-button uk-button-primary">Favorite&nbsp;<span class="uk-hidden-small uk-hidden-xsmall">&nbsp;</span><i class="uk-icon-star<?php echo ($board->favorite) ? '' : '-o'; ?>"></i></button>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <button <?php echo (isset($_SESSION['user'])) ? 'id="sidebar-newtopic"' : 'data-uk-modal="{ target: \'#modal-login\', bgclose: false, center: true }"'; ?> class="uk-button uk-button-primary" <?php echo ($board->isArchived || (isset($_SESSION['user']) && $_SESSION['user']->isBanned)) ? 'disabled="disabled"' : ''; ?>>Create Topic&nbsp;<span class="uk-hidden-small uk-hidden-xsmall">&nbsp;</span><i class="uk-icon-chevron-circle-right"></i></button>
                    
                    </td></tr>
                </table>
            </div>
            
            <br />
        </div>
        
        <?php if ($topics) { ?>
        <?php foreach ($topics as $topic) { ?>
        <div class="uk-panel uk-panel-box uk-padding-remove">
            <div class="uk-grid uk-grid-collapse topic-padding">
                <div class="uk-hidden-xsmall uk-panel-badge topic-padding">
                    <div class="uk-text-small uk-float-right uk-text-bold topic-created"><?php echo GrokBB\Util::getTimespan($topic->created, 4); ?> ago</div>
                    
                    <br />
                    
                    <div class="uk-button-group uk-float-right topic-buttons">
                        <!-- <button id="topic-tagged-<?php echo $topic->tid; ?>" class="uk-button uk-button-link" data-uk-tooltip="{ pos: 'top-left' }" title="View All Tags"><i class="uk-icon uk-icon-tags"></i></button> -->
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']->isModerator == $board->id) { ?>
                        <button id="topic-moderate-<?php echo $topic->tid; ?>" class="uk-button uk-button-link">Moderate</button>
                        <?php } else { ?>
                        <button id="topic-report-<?php echo $topic->tid; ?>" class="uk-button uk-button-link">Report</button>
                        <?php } ?>
                        <button id="topic-share-<?php echo $topic->tid; ?>" class="uk-button uk-button-link" data-uk-modal="{ target: '#modal-share', bgclose: false, center: true }">Share</button>
                        <button id="topic-save-<?php echo $topic->tid; ?>" class="uk-button uk-button-link topic-buttons-link" style="<?php echo (in_array($topic->tid, $tSaved)) ? 'display: none' : ''; ?>">Save For Later</button>
                        <div id="topic-saved-<?php echo $topic->tid; ?>" class="uk-button uk-button-link topic-buttons-link" style="<?php echo (in_array($topic->tid, $tSaved)) ? '' : 'display: none'; ?>">
                            <?php if (!isset($_SESSION['user']) || (isset($_SESSION['user']) && $topic->id_ur != $_SESSION['user']->id)) { ?>
                            <span id="topic-unsave-<?php echo $topic->tid; ?>" class="topic-link-underline">Unsave</span>&nbsp;&nbsp;|&nbsp;
                            <?php } else { ?>
                            <span class="topic-link-underline" onclick="UIkit.notify('This topic was created by you. It can not be unsaved.', { status: 'info' });">Unsave</span>&nbsp;&nbsp;|&nbsp;
                            <?php } ?>
                            <a class="topic-link-underline" href="" data-uk-tooltip="{ pos: 'bottom-left' }" title="Download PDF" data-uk-modal="{ target: '#modal-share', bgclose: false, center: true }"><i class="uk-icon-file-pdf-o"></i></a>&nbsp;
                            <a class="topic-link-underline" href="" data-uk-tooltip="{ pos: 'bottom-left' }" title="Download TXT" data-uk-modal="{ target: '#modal-share', bgclose: false, center: true }"><i class="uk-icon-file-text-o"></i></a>
                        </div>
                        <br />
                    </div>
                    
                    <br />
                    
                    <div class="uk-float-right topic-tags">
                        <ul id="topic-tags-<?php echo $topic->tid; ?>"></ul>
                    </div>
                </div>
                
                <div id="topic-user-<?php echo $topic->id_ur; ?>" class="uk-width-xsmall-2-10 uk-width-small-1-10 uk-text-center gbb-padding topic-user" data-uk-tooltip="{ pos: 'bottom-left' }" title="Joined: <?php echo ltrim(GrokBB\Util::getTimespan($topic->joined, 1), 0); ?> ago<br>Click to View Profile">
                    <div class="uk-container-center">
                        <?php if (isset($topicMediaFirst[$topic->tid])) { ?>
                        <div class="uk-margin-bottom" style="max-height: 55px">
                            <?php if (preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $topicMediaFirst[$topic->tid]->url)) { ?>
                            <img src="<?php echo $topicMediaFirst[$topic->tid]->url; ?>" style="max-height: 65px">
                            <?php } else if (preg_match('/\.(mp3|wav|ogg)$/i', $topicMediaFirst[$topic->tid]->url)) { ?>
                            <i class="uk-icon uk-icon-file-audio-o uk-hidden-small uk-hidden-xsmall" style="font-size: 64px"></i>
                            <i class="uk-icon uk-icon-file-audio-o uk-visible-xsmall" style="font-size: 58px"></i>
                            <i class="uk-icon uk-icon-file-audio-o uk-visible-small" style="font-size: 52px"></i>
                            <?php } else if (preg_match('/\.(mp4|webm|ogv)$/i', $topicMediaFirst[$topic->tid]->url)) { ?>
                            <i class="uk-icon uk-icon-file-video-o uk-hidden-small uk-hidden-xsmall" style="font-size: 64px"></i>
                            <i class="uk-icon uk-icon-file-video-o uk-visible-xsmall" style="font-size: 58px"></i>
                            <i class="uk-icon uk-icon-file-video-o uk-visible-small" style="font-size: 52px"></i>
                            <?php } else if (preg_match('/(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/', $topicMediaFirst[$topic->tid]->url, $matches)) { ?>
                            <img src="<?php echo SITE_BASE_URL; ?>/img.php?ytube=<?php echo $matches[2]; ?>">
                            <?php } else if (preg_match('/youtu\.be\/(.*)/', $topicMediaFirst[$topic->tid]->url, $matches)) { ?>
                            <img src="<?php echo SITE_BASE_URL; ?>/img.php?ytube=<?php echo $matches[1]; ?>">
                            <?php } else if (preg_match('/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/', $topicMediaFirst[$topic->tid]->url, $matches)) { ?>
                            <img src="<?php echo SITE_BASE_URL; ?>/img.php?vimeo=<?php echo $matches[2]; ?>">
                            <?php } ?>
                        </div>
                        <?php } else { ?>
                        <img src="<?php echo SITE_BASE_URL; ?>/img.php?uid=<?php echo $topic->id_ur; ?>" width="60">
                        <?php } ?>
                        <div id="user-username-<?php echo $topic->id_ur; ?>" class="uk-text-small uk-text-bold gbb-spacing-small" style="position: relative; top: 3px"><?php echo $topic->username; ?></div>
                        <div class="gbb-spacing-small uk-text-small">
                            <?php echo GrokBB\User::reputation($topic->id_ur, $uStats, $board); ?>&nbsp;&nbsp;<i id="user-badges-<?php echo $topic->id_ur; ?>" class="uk-icon uk-icon-shield <?php echo (isset($bUsers[$topic->id_ur])) ? '' : 'uk-text-muted'; ?>" <?php echo (isset($bUsers[$topic->id_ur])) ? 'data-uk-tooltip="{ pos: \'top\' }" title="Click to View Badges"' : ''; ?>></i>
                        </div>
                    </div>
                </div>
                
                <div class="uk-width-xsmall-8-10 uk-width-small-5-10 uk-width-xlarge-6-10 gbb-padding">
                    <h3 class="uk-text-bold uk-margin-remove"><span data-uk-tooltip="{ pos: 'right' }" title="Click to View Topic"><a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/' . $topic->tid; ?>" class="topic-link"><?php echo $topic->title; ?></a>&nbsp;</span></h3>
                    
                    <div class="uk-width-1-1 uk-text-small uk-text-bold gbb-spacing-small">
                        Views:&nbsp;<div class="uk-badge uk-badge-success"><?php echo $topic->counter_views; ?></div>&nbsp;&nbsp;&nbsp;
                        Replies:&nbsp;<div class="uk-badge uk-badge-success"><?php echo $topic->counter_replies; ?></div>
                        <i class="uk-icon uk-icon-level-up">&nbsp;</i><div class="uk-badge uk-badge-danger" data-uk-tooltip="{ pos: 'top-left' }" title="New Replies"><?php echo ($newReplies[$topic->tid] != -1) ? (int) $newReplies[$topic->tid] : $topic->counter_replies; ?></div>
                        <?php echo ($topic->sticky) ? '&nbsp;&nbsp;&nbsp;&nbsp;<i class="uk-icon uk-icon-thumb-tack"></i>' : ''; ?>
                    </div>
                    
                    <div class="uk-width-1-1 gbb-spacing-large">
                        <a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/search/bcat/' . $topic->bcid; ?>">
                            <?php if ($topic->bcimage) { ?>
                            <img src="<?php echo SITE_BASE_URL . '/img.php?bid=' . $board->id . '&cat=' . $topic->bcid; ?>" width="200" height="40" border="0" data-uk-tooltip="{ pos: 'right' }" title="Click to Filter By This Category">
                            <?php } else { ?>
                            <figure class="uk-overlay" style="background-color: <?php echo $topic->bccolor; ?>" data-uk-tooltip="{ pos: 'right' }" title="Click to Filter By This Category">
                                <img src="<?php echo SITE_BASE_URL . '/img/category.png'; ?>" width="200" height="40" border="0" title="<?php echo $topic->bcname; ?>" alt="<?php echo $topic->bcname; ?>">
                                <figcaption class="uk-overlay-panel uk-flex uk-flex-middle"><?php echo $topic->bcname; ?></figcaption>
                            </figure>
                            <?php } ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($sort == 'qry' && $sqry['type'] != 'title') {
            // prefix all periods with a space, so that we can find words at the end of sentances
            $content = str_replace('.', ' .', strip_tags($topic->content));
            
            $eachPosition = GrokBB\Util::highlightText($content, $sqry['text']);
                        
            if ($eachPosition) {
                $eachCount = count($eachPosition);
        ?>
        <div class="uk-width-1 uk-text-center">
            <i class="uk-icon uk-icon-caret-down"></i>
            <i class="uk-icon uk-icon-caret-down"></i>
            <i class="uk-icon uk-icon-caret-down"></i>
            <i class="uk-icon uk-icon-caret-down"></i>
        </div>
        <div class="uk-panel uk-panel-box uk-panel-header topic-matches">
            <div class="uk-panel-badge uk-badge uk-badge-success"><?php echo $eachCount . ' Match' . (($eachCount > 1) ? 'es' : ''); ?></div>
            <h3 class="uk-panel-title uk-text-primary uk-text-bold">Matched Content</h3>
            <?php
            $pad = 100;
            
            foreach ($eachPosition as $match) {
                $matchString = '';
                $pre = $match['pos'] - $pad;
                $add = $pad;
                
                if ($pre < 0) {
                    $add = $pad + $pre;
                    $pre = 0;
                }
                
                // prefix
                $matchString .= substr($content, $pre, $add);
                
                // highlight
                $matchString .= '<mark>' . substr($content, $match['pos'], $match['len']) . '</mark>';
                
                // append
                $matchString .= substr($content, $match['pos'] + $match['len'], $pad);
                
                echo '<div class="uk-alert uk-alert-info" data-uk-alert>';
                // add the ellipses and restore our periods
                echo '... ' . str_replace(' .', '.', $matchString) . ' ...';
                echo '</div><hr />';
            }
            ?>
        </div>
        <?php
            }
        }
        ?>
        <?php } ?>
        <ul class="uk-pagination">
            <li class="<?php echo ($numPage <= 1) ? 'uk-disabled' : ''; ?>"><a <?php echo ($numPage <= 1) ? 'onclick="return false;"' : ''; ?> href="<?php echo (($numPage <= 1) ? '/#' : SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/?page=' . ($numPage - 1)); ?>"><i class="uk-icon-angle-double-left"></i></a></li>
            <?php for ($i = 1; $i <= $topicsByPageCount; $i++) { ?>
            <li class="<?php echo ($i == $numPage) ? 'uk-active' : ''; ?>"><a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/?page=' . $i; ?>"><?php echo $i; ?></a></li>
            <?php } ?>
            <li class="<?php echo ($numPage >= $topicsByPageCount) ? 'uk-disabled' : ''; ?>"><a <?php echo ($numPage >= $topicsByPageCount) ? 'onclick="return false;"' : ''; ?> href="<?php echo (($numPage >= $topicsByPageCount) ? '/#' : SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/?page=' . ($numPage + 1)); ?>"><i class="uk-icon-angle-double-right"></i></a></li>
        </ul>
        <?php } else { ?>
        <div class="uk-alert uk-alert-danger uk-margin-top-remove">Your search returned no topics.</div>
        <?php } ?>
        
        <?php } // private board ?>
    </div>
    <?php require('sidebar.php'); ?>
</div>

<div id="modal-welcome" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Welcome!</div>
                
                <div class="gbb-spacing">
                <span class="uk-visible-large">Hi, and welcome to your new community! You can start by customizing the sidebar and your branding by clicking <a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings" class="uk-button uk-button-primary">My Board Settings</a></span>
                <div class="uk-margin-top uk-visible-large"><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users" class="uk-button uk-button-primary">Users & Stats</a> will let you manage users, create badges, send announcements and view your daily / monthly statistics.</div>
                <div class="uk-margin-top uk-visible-large">Remember that you can always ask for <a href="<?php echo SITE_BASE_URL; ?>/g/GrokBB_Help" class="uk-button uk-button-primary">Help</a> if you need too.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-subscription" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Subscription Warning!</div>
                
                <div class="gbb-spacing">
                <?php
                if ($board->stripe_cancelled > 0) {
                    echo '<span class="uk-text-danger">Your board subscription has been cancelled. You must add a credit card or you will lose ownership in <strong>' . GrokBB\Util::getTimespan(strtotime('+30 days', $board->expires), 2) . '</strong></span>';
                } else if ($board->stripe_id === '0') {
                    echo '<span class="uk-text-danger">Your trial period has ended. You must add a credit card or you will lose ownership in <strong>' . GrokBB\Util::getTimespan(strtotime('+30 days', $board->expires), 2) . '</strong></span>';
                }
                ?>
                <br /><br />
                You can add a credit card and update your subscription plan in &nbsp;<a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings" class="uk-button uk-button-primary">My Board Settings</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modals common to Home and View are located in the sidebar -->

<?php require(SITE_BASE_APP . 'footer.php'); ?>