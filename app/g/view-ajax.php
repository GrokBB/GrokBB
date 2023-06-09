<?php
// the GrokBB config
require_once('../../cfg.php');

$topicID = (int) $_GET['tid'];
$replyID = (int) $_GET['rid'];

if ($_SESSION['board']->isArchived) {
    $_SESSION['topic']->locked = 1;
}

if (isset($_COOKIE['board_prefs'])) {
    $bp = json_decode($_COOKIE['board_prefs'], true);
    
    $rply = (isset($bp[$_SESSION['board']->id]['rply'])) ? $bp[$_SESSION['board']->id]['rply'] : '';
    if (!in_array($rply, array('old', 'new', 'res', 'sav'))) { $rply = 'old'; }
} else {
    $rply = 'old';
}

switch ($rply) {
    case 'old' :
        $orderBy = 'r.created';
        break;
    case 'new' :
        $orderBy = 'r.created DESC';
        break;
    case 'res' :
        $orderBy = 'r.counter_replies DESC, r.created';
        break;
    case 'sav' :
        $orderBy = 'r.counter_saves DESC, r.created';
        break;
}

$repliesSelect = array('r.id as rid', 'r.*', 'u.*', 'u.id as uid', 'r.created as created', 'r.updated as updated', 'r.counter_replies as counter_replies', 'uu.username as updated_username');
if (isset($_SESSION['user'])) { $repliesSelect[] = 'ur.saved'; } else { $repliesSelect[] = '0 as saved'; }

$replies = $GLOBALS['db']->getAll('reply r INNER JOIN ' . DB_PREFIX . 'user u ON r.id_ur = u.id LEFT JOIN ' . DB_PREFIX . 'user uu ON r.updated_id_ur = uu.id ' . ((isset($_SESSION['user'])) ? 'LEFT JOIN ' . DB_PREFIX . 'user_reply ur ON ' . $_SESSION['user']->id . ' = ur.id_ur AND r.id = ur.id_ry' : ''), array('r.id_tc' => $topicID, 'r.id_ry' => $replyID), $orderBy, $repliesSelect);

if ($replies) {
    $rUsers = array();
    $bUsers = array();
    $replyIDs = array();
    
    foreach ($replies as $reply) {
        $rUsers[] = $reply->id_ur;
        $replyIDs[] = $reply->rid;
    }
    
    // get all the topics created in response to these replies
    $topics = $GLOBALS['db']->getAll('topic', array('id_ry' => array('IN', $replyIDs)));
    
    $topicsByReplyID = array();
    
    foreach ($topics as $topic) {
        $topicsByReplyID[$topic->id_ry] = $topic;
    }
    
    $rUsers = array_keys(array_flip($rUsers));
    
    $uStats = new stdClass();
    $uStats->board = array();
    $uStats->users = array();
    
    $statistics = $GLOBALS['db']->getAll('user_board_stats', array('id_bd' => $_SESSION['board']->id, 'id_ur' => array('IN', $rUsers)));
    foreach ($statistics as $stats) { $uStats->board[$stats->id_ur] = $stats; }
    
    $statistics = $GLOBALS['db']->getAll('user', array('id' => array('IN', $rUsers)), false, array('id', 'counter_topics', 'counter_replies'));
    foreach ($statistics as $stats) { $uStats->users[$stats->id] = $stats; }
    
    $badges = $GLOBALS['db']->getAll('user_board_badge ubb LEFT JOIN ' . DB_PREFIX . 'board_badge bb ON ubb.id_bb = bb.id', array('bb.id_bd' => $_SESSION['board']->id, 'ubb.id_ur' => array('IN', $rUsers)), false, array('ubb.*', 'COUNT(ubb.id_ur) as counter_badges'), false, false, 'ubb.id_ur');
    foreach ($badges as $badge) { $bUsers[$badge->id_ur] = $badge->counter_badges; }
?>

<input id="reply-responses-count-<?php echo $replyID; ?>" type="hidden" value="<?php echo count($replies); ?>" />

<?php foreach ($replies as $reply) { ?>
<div class="uk-panel uk-panel-box uk-padding-remove uk-margin-top">
    <div class="uk-grid uk-grid-collapse reply-padding">
        <div class="uk-panel-badge reply-padding">
            <div class="uk-text-small uk-float-right">
                <span class="uk-float-right"><strong><?php echo GrokBB\Util::getTimespan($reply->created, 3); ?> ago</strong></span>
                <?php if ($reply->updated > 0) { ?>
                <br /><span class="uk-text-small uk-float-right"><strong>Last Updated:</strong> <span id="reply-content-updated-<?php echo $reply->rid; ?>"><?php echo GrokBB\Util::getTimespan($reply->updated, 1); ?> ago</span> (<span id="reply-content-updated-by-<?php echo $reply->id; ?>"><a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $reply->updated_id_ur; ?>"><?php echo $reply->updated_username; ?></a></span>)</span>
                <?php } ?>
            </div>
        </div>
        
        <div class="uk-width-xsmall-2-10 uk-width-small-1-10 uk-text-small gbb-padding reply-response-gutter" style="background-color: #EBF7FD">
            &nbsp;&nbsp;&nbsp;<i class="uk-icon-level-up gbb-icon-rotate" style="font-size: 300%"></i>
            
            <div class="gbb-spacing">
                <a href="#reply<?php echo $replyID; ?>"><span class="uk-hidden-small">Go To </span>Top</a><br />
                <a id="reply-responses-hide-<?php echo $replyID; ?>">Hide All</a>
            </div>
        </div>
        
        <div id="reply-user-<?php echo $reply->id_ur; ?>" class="uk-width-xsmall-2-10 uk-width-small-1-10 uk-text-center gbb-padding reply-user" data-uk-tooltip="{ pos: 'bottom-left' }" title="Joined: <?php echo ltrim(GrokBB\Util::getTimespan($reply->joined, 1), 0); ?> ago<br>Click to View Profile">
            <div class="uk-container-center">
                <img src="<?php echo SITE_BASE_URL; ?>/img.php?uid=<?php echo $reply->id_ur; ?>" width="60"><br />
                <span id="user-username-<?php echo $reply->id_ur; ?>" class="uk-text-small uk-text-bold user-username-<?php echo $reply->rid; ?>"><?php echo $reply->username; ?></span>
                <div class="gbb-spacing-small uk-text-small uk-margin-small-bottom">
                    <?php echo GrokBB\User::reputation($reply->id_ur, $uStats, $_SESSION['board']); ?>&nbsp;&nbsp;<i id="user-badges-<?php echo $reply->id_ur; ?>" class="uk-icon uk-icon-shield <?php echo ($bUsers[$reply->id_ur]) ? '' : 'uk-text-muted'; ?>" <?php echo ($bUsers[$reply->id_ur]) ? 'data-uk-tooltip="{ pos: \'top\' }" title="Click to View Badges"' : ''; ?>></i>
                </div>
            </div>
        </div>
        
        <a name="reply<?php echo $reply->rid; ?>"></a>
        
        <div class="uk-width-xsmall-6-10 uk-width-small-8-10 uk-padding-bottom-remove gbb-padding gbb-spacing-large">
            <div id="reply-content-<?php echo $reply->rid; ?>" class="gbb-spacing-large" style="<?php echo ($reply->updated > 0) ? 'padding-top: 15px' : ''; ?>">
                <?php if ($reply->deleted > 0) { ?>
                <div class="uk-alert uk-alert-danger" data-uk-alert>
                    This reply has been deleted.
                </div>
                <?php } else { echo $reply->content; } ?>
            </div>
            <div id="reply-content-edit-<?php echo $reply->rid; ?>" class="gbb-spacing-large" style="display: none; <?php echo ($reply->updated > 0) ? 'padding-top: 15px' : ''; ?>">
                <div class="uk-clearfix">
                    <button id="reply-content-update-<?php echo $reply->rid; ?>" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Save</button>
                    <button id="reply-content-cancel-<?php echo $reply->rid; ?>" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Cancel</button>
                    <div class="uk-text-small uk-align-right gbb-editor-characters"><span id="reply-editor-char-<?php echo $reply->rid; ?>">0</span> / 15,000 characters</div>
                </div>
                <textarea id="reply-editor-<?php echo $reply->rid; ?>"></textarea>
                <br />
            </div>
            <div id="reply-content-buttons-<?php echo $reply->rid; ?>" class="uk-clearfix">
                <?php if ($topicsByReplyID[$reply->rid]) { ?>
                <a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/' . $topicsByReplyID[$reply->rid]->id; ?>" class="uk-text-small uk-text-bold reply-responses">Continue the conversation<i class="uk-icon-long-arrow-right reply-continue"></i></a>
                <?php } ?>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-quote-create-<?php echo $reply->rid; ?>" <?php echo ($topicsByReplyID[$reply->rid] || $_SESSION['topic']->locked) ? 'disabled="disabled"' : ''; ?>>Reply</button>
                <?php if (isset($_SESSION['user']) && ($reply->id_ur == $_SESSION['user']->id || $_SESSION['user']->isModerator == $_SESSION['board']->id)) { ?>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-edit-<?php echo $reply->rid; ?>" <?php echo ($_SESSION['topic']->locked || ($reply->deleted > 0 && $reply->deleted_id_ur != $_SESSION['user']->id && $_SESSION['user']->isModerator == false) || ($reply->id_ur == 1 && $_SESSION['user']->id != 1)) ? 'disabled="disabled"' : ''; ?>>Edit</button>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-restore-<?php echo $reply->rid; ?>" style="<?php echo ($reply->deleted > 0) ? '' : 'display: none'; ?>" <?php echo ($_SESSION['topic']->locked || ($reply->deleted > 0 && $reply->deleted_id_ur != $_SESSION['user']->id && $_SESSION['user']->isModerator == false) || ($reply->id_ur == 1 && $_SESSION['user']->id != 1)) ? 'disabled="disabled"' : ''; ?>>Restore</button>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-delete-<?php echo $reply->rid; ?>" style="<?php echo ($reply->deleted > 0) ? 'display: none' : ''; ?>" <?php echo ($_SESSION['topic']->locked || ($reply->id_ur == 1 && $_SESSION['user']->id != 1)) ? 'disabled="disabled"' : ''; ?>>Delete</button>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-delete-cancel-<?php echo $reply->rid; ?>" style="display: none">Cancel</button>
                <button class="uk-button uk-button-danger uk-align-right topic-view-button" id="reply-delete-confirm-<?php echo $reply->rid; ?>" style="display: none">Confirm Delete</button>
                <?php } ?>
                <?php if (!isset($_SESSION['user']) || $reply->id_ur != $_SESSION['user']->id || $_SESSION['user']->isModerator == $_SESSION['board']->id) { ?>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-save-<?php echo $reply->rid; ?>" style="<?php echo ($reply->saved > 0) ? 'display: none' : ''; ?>">Save</button>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-unsave-<?php echo $reply->rid; ?>" style="<?php echo ($reply->saved > 0) ? '' : 'display: none'; ?>">Unsave</button>
                <?php } ?>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']->isModerator == $_SESSION['board']->id) { ?>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-moderate-<?php echo $reply->rid; ?>">Moderate</button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div id="reply-quote-div-<?php echo $reply->rid; ?>" style="display: none">
    <div class="uk-width-1 uk-text-center">
        <i class="uk-icon uk-icon-caret-down"></i>
        <i class="uk-icon uk-icon-caret-down"></i>
        <i class="uk-icon uk-icon-caret-down"></i>
        <i class="uk-icon uk-icon-caret-down"></i>
    </div>
    <div class="uk-panel uk-panel-box">
        <div class="uk-clearfix">
            <button id="reply-quote-insert-<?php echo $reply->id_ry . 'r' . $reply->rid; ?>" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Post Reply</button>
            <button id="reply-quote-cancel-<?php echo $reply->rid; ?>" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Cancel</button>
            <div class="uk-text-small uk-align-right gbb-editor-characters"><span id="reply-quote-editor-char-<?php echo $reply->rid; ?>">0</span> / 15,000 characters</div>
        </div>
        
        <textarea id="reply-quote-editor-<?php echo $reply->rid; ?>"></textarea>
    </div>
</div>
<?php } ?>

<?php } ?>