<?php
$topicID = (int) $_SESSION['objectid'];

// this must be calculated before we record a new view
list($newRepliesCount, $newReplies) = GrokBB\Topic::newReplies($topicID);

GrokBB\Topic::view($topicID);

$topicSelect = array('t.id as tid', 't.*', 'u.*', 'u.id as uid', 't.created as created', 't.updated as updated', 't.counter_replies as counter_replies', 'bc.id as bcid', 'bc.name as bcname', 'bc.color as bccolor', 'bc.image as bcimage', 'uu.username as updated_username');
if (isset($_SESSION['user'])) { $topicSelect[] = 'IF (t.id_ur = ' . $_SESSION['user']->id . ', 1, ut.saved) as saved'; } else { $topicSelect[] = '0 as saved'; }

$topic = $GLOBALS['db']->getOne('topic t INNER JOIN ' . DB_PREFIX . 'user u ON t.id_ur = u.id INNER JOIN ' . DB_PREFIX . 'board_category bc ON t.id_bc = bc.id LEFT JOIN ' . DB_PREFIX . 'user uu ON t.updated_id_ur = uu.id ' . ((isset($_SESSION['user'])) ? 'LEFT JOIN ' . DB_PREFIX . 'user_topic ut ON ' . $_SESSION['user']->id . ' = ut.id_ur AND t.id = ut.id_tc' : ''), array('t.id' => $topicID), false, $topicSelect);

if ($topic === false) {
    header('Location: ' . SITE_BASE_URL . '/error/template');
}

$topicMedia = $GLOBALS['db']->getAll('topic_media', array('id_tc' => $topic->tid), 'id');
$topicMediaCount = ($topicMedia) ? count($topicMedia) : 1;
$topicMediaBreak = true;

$GLOBALS['includeEditor'] = true;

if ($topicMedia) {
    $GLOBALS['includePlayer'] = true;
}

require(SITE_BASE_APP . 'header.php');

// do not allow a topic to be edited or restored when it is archived or locked
// Note: this check must be done before we override $topic->locked below
if ($board->isArchived || $topic->locked || 
    (
        // otherwise, if it's only been deleted ...
        $topic->deleted > 0 && 
        
        (
            // make sure the user is logged in and ...
            isset($_SESSION['user']) == false || 
            
            (
                // only allow the user who deleted it, or a moderator, to edit / restore it
                $_SESSION['user']->isModerator == false && $topic->deleted_id_ur != $_SESSION['user']->id
            )
        )
    )
) {
    $topic->lockedForRestore = 1;
} else {
    $topic->lockedForRestore = 0;
}

if ($board->isArchived || $topic->deleted > 0) {
    $topic->locked = 1;
}

$_SESSION['topic'] = GrokBB\Util::sanitizeSession($topic);

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

// any update to this query needs to be done in view-ajax.php too
$replies = $GLOBALS['db']->getAll('reply r INNER JOIN ' . DB_PREFIX . 'user u ON r.id_ur = u.id LEFT JOIN ' . DB_PREFIX . 'user uu ON r.updated_id_ur = uu.id ' . ((isset($_SESSION['user'])) ? 'LEFT JOIN ' . DB_PREFIX . 'user_reply ur ON ' . $_SESSION['user']->id . ' = ur.id_ur AND r.id = ur.id_ry' : ''), array('r.id_tc' => $topicID, 'r.id_ry' => 0), $orderBy, $repliesSelect);

$rUsers = array($topic->id_ur);
$bUsers = array();

if ($replies) {    
    foreach ($replies as $reply) {
        $rUsers[] = $reply->id_ur;
    }
}

$rUsers = array_keys(array_flip($rUsers));

$uStats = new stdClass();
$uStats->board = array();
$uStats->users = array();

$statistics = $GLOBALS['db']->getAll('user_board_stats', array('id_bd' => $board->id, 'id_ur' => array('IN', $rUsers)));
foreach ($statistics as $stats) { $uStats->board[$stats->id_ur] = $stats; }

$statistics = $GLOBALS['db']->getAll('user', array('id' => array('IN', $rUsers)), false, array('id', 'counter_topics', 'counter_replies'));
foreach ($statistics as $stats) { $uStats->users[$stats->id] = $stats; }

$badges = $GLOBALS['db']->getAll('user_board_badge ubb LEFT JOIN ' . DB_PREFIX . 'board_badge bb ON ubb.id_bb = bb.id', array('bb.id_bd' => $board->id, 'ubb.id_ur' => array('IN', $rUsers)), false, array('ubb.*', 'COUNT(ubb.id_ur) as counter_badges'), false, false, 'ubb.id_ur');
foreach ($badges as $badge) { $bUsers[$badge->id_ur] = $badge->counter_badges; }
?>

<div id="topic-view" class="uk-grid uk-grid-small" data-uk-grid-margin>
    <div class="uk-width-small-1-1 uk-width-large-3-4">
        <?php if ($topic->private && (isset($_SESSION['user']) == false || $_SESSION['user']->isModerator == false)) { ?>
        <div class="uk-alert uk-alert-danger">You do not have access to view this topic.</div>
        <?php } else { ?>
        <div class="uk-panel uk-panel-box uk-padding-remove">
            <div class="uk-grid uk-grid-collapse topic-padding">
                <div class="uk-hidden-xsmall uk-panel-badge topic-padding">
                    <div class="uk-text-small uk-float-right uk-text-bold topic-created"><?php echo GrokBB\Util::getTimespan($topic->created); ?> ago</div>
                    
                    <br />
                    
                    <div class="uk-button-group uk-float-right topic-buttons">
                        <!-- <button id="topic-tagged-<?php echo $topic->tid; ?>" class="uk-button uk-button-link"><i class="uk-icon uk-icon-tags"></i></button> -->
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']->isModerator == $board->id) { ?>
                        <button id="topic-moderate-<?php echo $topic->tid; ?>" class="uk-button uk-button-link">Moderate</button>
                        <?php } else { ?>
                        <button id="topic-report-<?php echo $topic->tid; ?>" class="uk-button uk-button-link">Report</button>
                        <?php } ?>
                        <button id="topic-share" class="uk-button uk-button-link" data-uk-modal="{ target: '#modal-share', bgclose: false, center: true }">Share</button>
                        <button id="topic-save" class="uk-button uk-button-link topic-buttons-link" style="<?php echo ($topic->saved) ? 'display: none' : ''; ?>">Save For Later</button>
                        <div id="topic-saved" class="uk-button uk-button-link topic-buttons-link" style="<?php echo ($topic->saved) ? '' : 'display: none'; ?>">
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
                        <ul id="topic-tags"></ul>
                    </div>
                </div>
                
                <div id="topic-user-<?php echo $topic->id_ur; ?>" class="uk-width-xsmall-2-10 uk-width-small-1-10 uk-text-center gbb-padding topic-user" data-uk-tooltip="{ pos: 'bottom-left' }" title="Joined: <?php echo ltrim(GrokBB\Util::getTimespan($topic->joined, 1), 0); ?> ago<br>Click to View Profile">
                    <div class="uk-container-center">
                        <img src="<?php echo SITE_BASE_URL; ?>/img.php?uid=<?php echo $topic->id_ur; ?>" width="60"><br />
                        <span id="user-username-<?php echo $topic->id_ur; ?>" class="uk-text-small uk-text-bold"><?php echo $topic->username; ?></span>
                        <div class="gbb-spacing-small uk-text-small">
                            <?php echo GrokBB\User::reputation($topic->id_ur, $uStats, $board); ?>&nbsp;&nbsp;<i id="user-badges-<?php echo $topic->id_ur; ?>" class="uk-icon uk-icon-shield <?php echo (isset($bUsers[$topic->id_ur])) ? '' : 'uk-text-muted'; ?>" <?php echo (isset($bUsers[$topic->id_ur])) ? 'data-uk-tooltip="{ pos: \'top\' }" title="Click to View Badges"' : ''; ?>></i>
                        </div>
                    </div>
                </div>
                
                <div class="uk-width-xsmall-8-10 uk-width-small-5-10 uk-width-xlarge-6-10 gbb-padding">
                    <?php if ($_SESSION['user'] && ($_SESSION['user']->id == $topic->id_ur || $_SESSION['user']->isModerator == $board->id)) { ?>
                    <h3 class="uk-text-bold uk-margin-remove"><span id="topic-title" data-uk-tooltip="{ pos: 'right' }" title="Click to Edit Title"><?php echo $topic->title; ?>&nbsp;</span></h3>
                    <div class="uk-form uk-form-stacked"><input type="text" id="topic-title-edit" maxlength="60" class="uk-form-controls uk-form-width-large" value="<?php echo $topic->title; ?>" style="display: none"></div>
                    <?php } else { ?>
                    <h3 class="uk-text-bold uk-margin-remove"><?php echo $topic->title; ?></h3>
                    <?php } ?>
                    
                    <div class="uk-width-1-1 uk-text-small uk-text-bold gbb-spacing-small">
                        Views:&nbsp;<div class="uk-badge uk-badge-success"><?php echo $topic->counter_views; ?></div>&nbsp;&nbsp;&nbsp;
                        Replies:&nbsp;<div class="uk-badge uk-badge-success"><?php echo $topic->counter_replies; ?></div>
                        <i class="uk-icon-level-up">&nbsp;</i><div class="uk-badge uk-badge-danger" data-uk-tooltip="{ pos: 'top-left' }" title="New Replies"><?php echo ($newRepliesCount != -1) ? (int) $newRepliesCount : $topic->counter_replies ?></div>
                    </div>
                    
                    <div class="uk-width-1-1 gbb-spacing-large">
                        <?php if ($_SESSION['user'] && ($_SESSION['user']->id == $topic->id_ur || $_SESSION['user']->isModerator == $board->id)) { ?>
                        <?php $categoryTooltip = 'Click to Change Category'; ?>
                        <a data-uk-modal="{ target: '#modal-change-category', bgclose: false, center: true }">
                        <?php } else { ?>
                        <?php $categoryTooltip = 'Click to Filter By This Category'; ?>
                        <a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/search/bcat/' . $topic->bcid; ?>">
                        <?php } ?>
                            <?php if ($topic->bcimage) { ?>
                            <img src="<?php echo SITE_BASE_URL . '/img.php?bid=' . $board->id . '&cat=' . $topic->bcid; ?>" width="200" height="40" border="0" data-uk-tooltip="{ pos: 'right' }" title="<?php echo $categoryTooltip; ?>">
                            <?php } else { ?>
                            <figure class="uk-overlay" style="background-color: <?php echo $topic->bccolor; ?>" data-uk-tooltip="{ pos: 'right' }" title="<?php echo $categoryTooltip; ?>">
                                <img src="<?php echo SITE_BASE_URL . '/img/category.png'; ?>" width="200" height="40" border="0" title="<?php echo $topic->bcname; ?>" alt="<?php echo $topic->bcname; ?>">
                                <figcaption class="uk-overlay-panel uk-flex uk-flex-middle"><?php echo $topic->bcname; ?></figcaption>
                            </figure>
                            <?php } ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-width-1 uk-text-center">
            <i class="uk-icon uk-icon-caret-down"></i>
            <i class="uk-icon uk-icon-caret-down"></i>
            <i class="uk-icon uk-icon-caret-down"></i>
            <i class="uk-icon uk-icon-caret-down"></i>
        </div>
        <div class="uk-panel uk-panel-box">
            <?php
            if ($topic->id_ry) {
                // locate the existing reply this topic was started from
                $reply = $GLOBALS['db']->getOne('reply r INNER JOIN ' . DB_PREFIX . 'user u ON r.id_ur = u.id INNER JOIN ' . DB_PREFIX . 'topic t ON r.id_tc = t.id', 
                    array('r.id' => $topic->id_ry), false, array('r.*', 'u.username', 't.title', 't.id_bc'));
            ?>
            <div class="uk-form-row uk-margin-bottom">
                <label class="uk-form-label">
                    Replying To <a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/' . $reply->id_tc . '#open' . $reply->id_ry . 'reply' . $reply->id; ?>"><?php echo $reply->title; ?></a>
                    &nbsp;/&nbsp;
                    Response By <a href="<?php echo SITE_BASE_URL . '/user/view/' . $reply->id_ur; ?>"><?php echo $reply->username; ?></a>
                </label>
                <div id="topic-replyHTML" class="gbb-spacing"><?php echo $reply->content; ?></div>
            </div>
            <?php } ?>
            <?php if ($topic->deleted > 0) { ?>
            <div id="topic-content" class="uk-alert uk-alert-danger">
                This topic has been deleted.
            </div>
            <?php } else { ?>
            <div id="topic-content" class="uk-alert uk-alert-info" data-uk-alert>
                <?php echo $topic->content; ?>
            </div>
            <?php if ($topicMedia) { $topicMediaFirst = $topicMedia[0]; ?>
            <div id="topic-media">
                <div class="uk-grid uk-grid-medium" data-uk-grid-margin>
                    <div class="uk-width-xsmall-1-1 uk-width-xlarge-5-10">
                        <div class="uk-block uk-block-muted uk-border-rounded uk-margin-bottom gbb-padding">
                            <div class="uk-text-center">
                                <!-- see // https://bugzilla.mozilla.org/show_bug.cgi?id=279048 for the iFrame videos -->
                                <?php if (preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $topicMediaFirst->url)) { ?>
                                <img src="<?php echo $topicMediaFirst->url; ?>">
                                <?php } else if (preg_match('/\.(mp3|wav|ogg)$/i', $topicMediaFirst->url)) { ?>
                                <audio class="uk-responsive-width" src="<?php echo $topicMediaFirst->url; ?>" style="width: 560px" preload="auto" controls></audio>
                                <?php } else if (preg_match('/\.(mp4|webm|ogv)$/i', $topicMediaFirst->url)) { ?>
                                <video class="uk-responsive-width" src="<?php echo $topicMediaFirst->url; ?>" width="560" height="315" preload="auto" controls></video>
                                <?php } else if (preg_match('/(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/', $topicMediaFirst->url, $matches)) { ?>
                                <iframe id="mediaVideo" src="https://www.youtube.com/embed/<?php echo $matches[2]; ?>" width="560" height="315" style="max-width: 100%"></iframe>
                                <?php } else if (preg_match('/youtu\.be\/(.*)/', $topicMediaFirst->url, $matches)) { ?>
                                <iframe id="mediaVideo" src="https://www.youtube.com/embed/<?php echo $matches[1]; ?>" width="560" height="315" style="max-width: 100%"></iframe>
                                <?php } else if (preg_match('/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/', $topicMediaFirst->url, $matches)) { ?>
                                <iframe id="mediaVideo" src="https://player.vimeo.com/video/<?php echo $matches[2]; ?>" width="560" height="315" style="max-width: 100%"></iframe>
                                <?php } else { $topicMediaBreak = false; ?>
                                <div class="uk-alert uk-alert-danger uk-margin-bottom-remove uk-text-left" data-uk-alert><i class="uk-icon uk-icon-exclamation-triangle"></i>&nbsp;&nbsp;Unsupported Media</div>
                                <?php } ?>
                                <div class="uk-grid uk-text-small gbb-spacing">
                                    <div class="uk-width-xsmall-5-10 uk-width-small-7-10 uk-text-left">
                                        <?php if ($topicMediaFirst->txt) { echo $topicMediaFirst->txt; } ?>
                                    </div>
                                    <div class="uk-width-xsmall-5-10 uk-width-small-3-10">
                                        <a id="topic-media-button" href="#" onclick="lightbox.show(); return false;" class="uk-align-right uk-margin-bottom-remove"><?php if ($topicMediaCount > 1) { ?>View All <?php echo $topicMediaCount; ?> Media Files<?php } else { ?>View Larger<?php } ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
            var lightbox = UIkit.lightbox.create([
                <?php
                $tmNum = 0;
                
                foreach ($topicMedia as $media) {
                    $tmNum++; $dims = '';
                    
                    if (preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $media->url)) {
                        $type = 'image';
                    } else if (preg_match('/\.(mp3|wav|ogg)$/i', $media->url)) {
                        $type = 'audio';
                    } else if (preg_match('/\.(mp4|webm|ogv)$/i', $media->url)) {
                        $type = 'video';
                        $dims = ", 'width': 1280, 'height': 720";
                    } else if (preg_match('/(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/', $media->url)) {
                        $type = 'youtube';
                        $dims = ", 'width': 1280, 'height': 720";
                    } else if (preg_match('/youtu\.be\/(.*)/', $media->url)) {
                        $type = 'youtube';
                        $dims = ", 'width': 1280, 'height': 720";
                    } else if (preg_match('/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/', $media->url)) {
                        $type = 'vimeo';
                        $dims = ", 'width': 1280, 'height': 720";
                    }
                    
                    echo "{ 'source': '" . $media->url . "', 'type': '" . $type . "', 'title': '" . str_replace("'", "\'", $media->txt) . "'" . $dims . " }";
                    if ($tmNum < $topicMediaCount) { echo ","; }
                }
                ?>
            ]);
            </script>
            <?php } ?>
            <?php } ?>
            <div id="topic-content-edit" style="display: none">
                <div class="uk-clearfix">
                    <button id="topic-edit-update" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Save</button>
                    <button id="topic-edit-cancel" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Cancel</button>
                    <div class="uk-text-small uk-align-right gbb-editor-characters"><span id="topic-editor-char">0</span> / 15,000 characters</div>
                </div>
                
                <textarea id="topic-editor"><?php echo $topic->content_md; ?></textarea>
            </div>
            <div id="topic-media-edit" class="uk-block uk-block-muted gbb-spacing gbb-padding-large" style="display: none">
                <div class="uk-form uk-form-stacked">
                    <div class="uk-form-row">
                        <label class="uk-form-label">Your Media</label>
                        <span class="uk-text-small">
                            If your topic refers to a picture, video or audio file on another site then you can add a link to it here, and it will automatically display under your content. If more than one link is added then a slideshow will be available. 
                            You can link to any browser supported image type or the following audio / video formats: YouTube, Vimeo, MP4, WebM, OGV, MP3, Wave, OGG
                        </span>
                        <div id="topic-media-all" class="uk-grid uk-grid-small">
                            <?php for ($mNum = 0; $mNum < $topicMediaCount; $mNum++) { ?>
                            <div class="uk-width-8-10 gbb-spacing topic-media-div">
                                <input class="uk-width-1-1 topic-media-url" type="text" maxlength="2000" value="<?php echo (isset($topicMedia[$mNum])) ? $topicMedia[$mNum]->url : ''; ?>">
                                <div class="uk-margin-top uk-margin-bottom">
                                    <strong>Caption</strong>&nbsp;&nbsp;<input class="uk-width-1-2 topic-media-txt" type="text" maxlength="120" value="<?php echo (isset($topicMedia[$mNum])) ? $topicMedia[$mNum]->txt : ''; ?>">
                                </div>
                            </div>
                            <?php if ($mNum == 0) { ?>
                            <div class="uk-width-2-10 gbb-spacing">
                                <a class="uk-button uk-button-primary topic-media-add"><span class="uk-hidden-xsmall">Add New Link</span><span class="uk-visible-xsmall">Add</span></a>
                            </div>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="topic-content-buttons">
                <button class="uk-button uk-button-primary" id="topic-reply-create" <?php echo ($topic->locked) ? 'disabled="disabled"' : ''; ?>>Reply</button>
                <?php if ($topic->updated > 0) { ?>
                &nbsp;&nbsp;<span class="uk-text-small"><strong>Last Updated:</strong> <span id="topic-content-updated"><span class="uk-hidden-xsmall"><?php echo GrokBB\Util::getTimespan($topic->updated, 3); ?></span><span class="uk-visible-xsmall"><?php echo GrokBB\Util::getTimespan($topic->updated, 2); ?></span> ago</span> (<span id="topic-content-updated-by"><a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $topic->updated_id_ur; ?>"><?php echo $topic->updated_username; ?></a></span>)</span><span class="uk-visible-xsmall"><br /><br /></span>
                <?php } ?>
                
                <?php if ($_SESSION['user'] && ($_SESSION['user']->id == $topic->id_ur || $_SESSION['user']->isModerator == $board->id)) { ?>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="topic-edit" <?php echo ($topic->lockedForRestore || ($topic->id_ur == 1 && $_SESSION['user']->id != 1)) ? 'disabled="disabled"' : ''; ?>>Edit</button>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="topic-restore" style="<?php echo ($topic->deleted > 0) ? '' : 'display: none'; ?>" <?php echo ($topic->lockedForRestore || ($topic->id_ur == 1 && $_SESSION['user']->id != 1)) ? 'disabled="disabled"' : ''; ?>>Restore</button>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="topic-delete" style="<?php echo ($topic->deleted > 0) ? 'display: none' : ''; ?>" <?php echo ($topic->locked || ($topic->id_ur == 1 && $_SESSION['user']->id != 1)) ? 'disabled="disabled"' : ''; ?>>Delete</button>
                <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="topic-delete-cancel" style="display: none">Cancel</button>
                <button class="uk-button uk-button-danger uk-align-right topic-view-button" id="topic-delete-confirm" style="display: none">Confirm Delete</button>
                <?php } ?>
            </div>
        </div>
        <div id="topic-reply-div" style="display: none">
            <div class="uk-width-1 uk-text-center">
                <i class="uk-icon uk-icon-caret-down"></i>
                <i class="uk-icon uk-icon-caret-down"></i>
                <i class="uk-icon uk-icon-caret-down"></i>
                <i class="uk-icon uk-icon-caret-down"></i>
            </div>
            <div class="uk-panel uk-panel-box">
                <div class="uk-clearfix">
                    <button id="topic-reply-insert" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Post Reply</button>
                    <button id="topic-reply-cancel" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Cancel</button>
                    <div class="uk-text-small uk-align-right gbb-editor-characters"><span id="topic-reply-editor-char">0</span> / 15,000 characters</div>
                </div>
                
                <textarea id="topic-reply-editor"></textarea>
            </div>
        </div>
        <?php if ($replies) { ?>
        <br />
        
        <a name="sort"></a>
        
        <div class="uk-container-center uk-width-small-7-10 uk-width-medium-6-10 uk-width-xlarge-4-10 gbb-spacing">
            <span class="uk-text-small uk-text-bold uk-hidden-xsmall">Sort Replies By</span>&nbsp;
            <a id="reply-sort-old" class="uk-button uk-button-mini gbb-button-static <?php echo ($rply == 'old') ? 'uk-button-primary' : ''; ?>">Oldest First</a>&nbsp;
            <a id="reply-sort-new" class="uk-button uk-button-mini gbb-button-static <?php echo ($rply == 'new') ? 'uk-button-primary' : ''; ?>">Newest First</a>&nbsp;
            <a id="reply-sort-res" class="uk-button uk-button-mini gbb-button-static <?php echo ($rply == 'res') ? 'uk-button-primary' : ''; ?>">Most Responses</a>&nbsp;
            <a id="reply-sort-sav" class="uk-button uk-button-mini gbb-button-static <?php echo ($rply == 'sav') ? 'uk-button-primary' : ''; ?>">Most Saves</a>&nbsp;
        </div>
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
                
                <div id="reply-user-<?php echo $reply->id_ur; ?>" class="uk-width-xsmall-2-10 uk-width-small-1-10 uk-text-center gbb-padding reply-user" data-uk-tooltip="{ pos: 'bottom-left' }" title="Joined: <?php echo ltrim(GrokBB\Util::getTimespan($reply->joined, 1), 0); ?> ago<br>Click to View Profile">
                    <div class="uk-container-center">
                        <img src="<?php echo SITE_BASE_URL; ?>/img.php?uid=<?php echo $reply->id_ur; ?>" width="60"><br />
                        <span id="user-username-<?php echo $reply->id_ur; ?>" class="uk-text-small uk-text-bold"><?php echo $reply->username; ?></span>
                        <div class="gbb-spacing-small uk-text-small uk-margin-small-bottom">
                            <?php echo GrokBB\User::reputation($reply->id_ur, $uStats, $board); ?>&nbsp;&nbsp;<i id="user-badges-<?php echo $reply->id_ur; ?>" class="uk-icon uk-icon-shield <?php echo ($bUsers[$reply->id_ur]) ? '' : 'uk-text-muted'; ?>" <?php echo ($bUsers[$reply->id_ur]) ? 'data-uk-tooltip="{ pos: \'top\' }" title="Click to View Badges"' : ''; ?>></i>
                        </div>
                    </div>
                </div>
                
                <a name="reply<?php echo $reply->rid; ?>"></a>
                
                <div class="uk-width-xsmall-8-10 uk-width-small-9-10 uk-padding-bottom-remove gbb-padding gbb-spacing-large">
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
                        <?php if ($reply->counter_replies > 0) { ?>
                        <a id="reply-responses-show-<?php echo $reply->rid; ?>" class="uk-text-small uk-text-bold reply-responses">Show the <span id="reply-responses-show-count-<?php echo $reply->rid; ?>"><?php echo $reply->counter_replies; ?></span> response<?php echo ($reply->counter_replies > 1) ? 's' : ''; ?> to this user<?php echo (isset($newReplies[$reply->rid])) ? '&nbsp;<span class="reply-responses-new">(' . $newReplies[$reply->rid] . ' new)</span>' : ''; ?></a>
                        <a id="reply-responses-hide-<?php echo $reply->rid; ?>" class="uk-text-small uk-text-bold reply-responses" style="display: none">Hide the <span id="reply-responses-hide-count-<?php echo $reply->rid; ?>"><?php echo $reply->counter_replies; ?></span> response<?php echo ($reply->counter_replies > 1) ? 's' : ''; ?> to this user<?php echo (isset($newReplies[$reply->rid])) ? '&nbsp;<span class="reply-responses-new">(' . $newReplies[$reply->rid] . ' new)</span>' : ''; ?></a>
                        <span class="uk-visible-xsmall"><br /><br /></span>
                        <?php } ?>
                        <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-reply-create-<?php echo $reply->rid; ?>" <?php echo ($topic->locked) ? 'disabled="disabled"' : ''; ?>>Reply</button>
                        <?php if (isset($_SESSION['user']) && ($reply->id_ur == $_SESSION['user']->id || $_SESSION['user']->isModerator == $board->id)) { ?>
                        <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-edit-<?php echo $reply->rid; ?>" <?php echo ($topic->locked || ($reply->deleted > 0 && $reply->deleted_id_ur != $_SESSION['user']->id && $_SESSION['user']->isModerator == false) || ($reply->id_ur == 1 && $_SESSION['user']->id != 1)) ? 'disabled="disabled"' : ''; ?>>Edit</button>
                        <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-restore-<?php echo $reply->rid; ?>" style="<?php echo ($reply->deleted > 0) ? '' : 'display: none'; ?>" <?php echo ($topic->locked || ($reply->deleted > 0 && $reply->deleted_id_ur != $_SESSION['user']->id && $_SESSION['user']->isModerator == false) || ($reply->id_ur == 1 && $_SESSION['user']->id != 1)) ? 'disabled="disabled"' : ''; ?>>Restore</button>
                        <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-delete-<?php echo $reply->rid; ?>" style="<?php echo ($reply->deleted > 0) ? 'display: none' : ''; ?>" <?php echo ($topic->locked || ($reply->id_ur == 1 && $_SESSION['user']->id != 1)) ? 'disabled="disabled"' : ''; ?>>Delete</button>
                        <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-delete-cancel-<?php echo $reply->rid; ?>" style="display: none">Cancel</button>
                        <button class="uk-button uk-button-danger uk-align-right topic-view-button" id="reply-delete-confirm-<?php echo $reply->rid; ?>" style="display: none">Confirm Delete</button>
                        <?php } ?>
                        <?php if (!isset($_SESSION['user']) || $reply->id_ur != $_SESSION['user']->id || $_SESSION['user']->isModerator == $board->id) { ?>
                        <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-save-<?php echo $reply->rid; ?>" style="<?php echo ($reply->saved > 0) ? 'display: none' : ''; ?>">Save</button>
                        <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-unsave-<?php echo $reply->rid; ?>" style="<?php echo ($reply->saved > 0) ? '' : 'display: none'; ?>">Unsave</button>
                        <?php } ?>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']->isModerator == $board->id) { ?>
                        <button class="uk-button uk-button-primary uk-align-right topic-view-button" id="reply-moderate-<?php echo $reply->rid; ?>">Moderate</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="reply-reply-div-<?php echo $reply->rid; ?>" style="display: none">
            <div class="uk-width-1 uk-text-center">
                <i class="uk-icon uk-icon-caret-down"></i>
                <i class="uk-icon uk-icon-caret-down"></i>
                <i class="uk-icon uk-icon-caret-down"></i>
                <i class="uk-icon uk-icon-caret-down"></i>
            </div>
            <div class="uk-panel uk-panel-box">
                <div class="uk-clearfix">
                    <button id="reply-reply-insert-<?php echo $reply->rid; ?>" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Post Reply</button>
                    <button id="reply-reply-cancel-<?php echo $reply->rid; ?>" class="uk-button uk-button-primary uk-align-right gbb-editor-button topic-view-button">Cancel</button>
                    <div class="uk-text-small uk-align-right gbb-editor-characters"><span id="reply-reply-editor-char-<?php echo $reply->rid; ?>">0</span> / 15,000 characters</div>
                </div>
                
                <textarea id="reply-reply-editor-<?php echo $reply->rid; ?>"></textarea>
            </div>
        </div>
        <div id="reply-responses-div-<?php echo $reply->rid; ?>" style="display: none">
            <div class="uk-alert uk-alert-info gbb-spacing-large" data-uk-alert>
                <i class="uk-icon-spinner uk-icon-spin"></i>&nbsp;&nbsp;Loading Responses ...
            </div>
        </div>
        <?php } ?>
        <?php } ?>
        <?php } // private ?>
    </div>
    <?php require('sidebar.php'); ?>
</div>

<div id="modal-change-category" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Choose New Category</div>
                
                <div class="uk-grid">
                    <?php
                    foreach ($categories as $category) {
                        if ($category->id == $topic->id_bc) { continue; }
                    ?>
                    <div class="uk-width-1-4">
                        <table cellpadding="5" cellspacing="0">
                        <tr>
                            <td>
                                <a id="change-category-<?php echo $category->id; ?>">
                                    <?php if ($category->image) { ?>
                                    <img src="<?php echo SITE_BASE_URL . '/img.php?bid=' . $board->id . '&cat=' . $category->id; ?>" width="200" height="40" data-uk-tooltip="{ pos: 'top-right' }" title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>">
                                    <?php } else { ?>
                                    <figure class="uk-overlay" style="background-color: <?php echo $category->color; ?>">
                                        <img src="<?php echo SITE_BASE_URL . '/img/category.png'; ?>" width="200" height="40" title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>">
                                        <figcaption class="uk-overlay-panel uk-flex uk-flex-middle"><?php echo $category->name; ?></figcaption>
                                    </figure>
                                    <?php } ?>
                                </a>
                            </td>
                        </tr>
                        </table>
                    </div>
                    <?php } ?>
                </div>
                
                <div class="uk-width-1-1 uk-text-right gbb-spacing-large">
                    <a class="uk-button uk-button-primary uk-modal-close">Cancel & Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-reply" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Create New Topic</div>
                
                You must create a <a id="modal-reply-create" href="" class="uk-button uk-button-primary">New Topic</a> to continue this conversation. 
                We do this to keep the responses uncluttered and related to the original topic.
                <br /><br />
                Don't worry, by clicking the button above, your new topic will be linked to this user's response, and so other users will be able to find it and reply there.
                
                <div class="uk-width-1-1 uk-text-right gbb-spacing-large">
                    <a class="uk-button uk-button-primary uk-modal-close">Cancel & Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-moderate-reply" class="uk-modal">
    <input type="hidden" id="modal-moderate-reply-id" value="0" />
    <input type="hidden" id="modal-moderate-reply-username" value="" />
    <input type="hidden" id="modal-moderate-reply-userid" value="0" />
    
    <div class="uk-modal-dialog">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Moderate<br /><span id="modal-moderate-reply-title" class="uk-text-small"></span></div>
                
                <ul class="uk-tab uk-tab-grid" data-uk-tab="{ connect: '#moderate-reply-tabs' }">
                    <li class="uk-width-1-3"><a href="#" class="moderate-border">User</a></li>
                </ul>
                
                <ul id="moderate-reply-tabs" class="uk-switcher">
                    <li class="uk-panel uk-panel-box uk-panel-box-secondary moderate-border">
                        <strong>Reply Creator:</strong>&nbsp;&nbsp;<span id="modal-moderate-reply-user"></span>
                        
                        <br /><br />
                        
                        <?php if ($board->type == 1 || $board->type == 2) { ?>
                        <input type="checkbox" id="modal-moderate-reply-approved" />&nbsp;this user is approved to <?php echo ($board->type == 2) ? 'post' : 'read and post'; ?> topics<br />
                        <?php } ?>
                        <?php if ($board->type == 0 || $board->type == 2) { ?>
                        <input type="checkbox" id="modal-moderate-reply-banned" />&nbsp;ban this user from reading and posting topics<br />
                        <?php } ?>
                        
                        <br />
                        
                        <div class="uk-text-bold">Moderator Points</div>
                        <div class="uk-text-small gbb-spacing">
                            You can award up to 3 moderator points to this user, for this reply. These points will increase the user's reputation on your board, 
                            and it will allow others to see who provides quality content.
                        </div>
                        <div class="gbb-spacing">
                            <i id="modal-moderate-reply-points-0" class="uk-icon uk-icon-times-circle-o" style="font-size: 200%"></i>
                            &nbsp;
                            <i id="modal-moderate-reply-points-1" class="uk-icon uk-icon-certificate" style="font-size: 200%"></i>
                            &nbsp;
                            <i id="modal-moderate-reply-points-2" class="uk-icon uk-icon-certificate" style="font-size: 200%"></i>
                            &nbsp;
                            <i id="modal-moderate-reply-points-3" class="uk-icon uk-icon-certificate" style="font-size: 200%"></i>
                            &nbsp;
                            <span id="modal-moderate-reply-points-text"></span>
                        </div>
                        
                        <br />
                        
                        <div id="modal-moderate-reply-duplicate-none" class="uk-text-small" style="display: none">
                            <div class="uk-alert uk-alert-info uk-margin-remove">
                                There are no other users posting from the same IP address.
                            </div>
                        </div>
                        
                        <div id="modal-moderate-reply-duplicate-warn" class="uk-text-small" style="display: none">
                            <div id="modal-moderate-reply-duplicate-warn-toggle" class="uk-alert uk-alert-danger uk-margin-remove" data-uk-tooltip="{ pos: 'bottom' }" title="Click to View Details">
                                <i class="uk-icon uk-icon-exclamation-triangle"></i>&nbsp;&nbsp;Duplicate IP Address
                                <i id="modal-moderate-reply-duplicate-warn-icon" class="uk-icon uk-icon-caret-square-o-up uk-align-right gbb-icon-large"></i>
                                
                                <div id="modal-moderate-reply-duplicate-warn-more" style="display: none">
                                <br />
                                
                                The IP address this user posted from is associated with other users on this board.<br />However, there may be a legitimate reason for this ...
                                
                                <ul>
                                    <li class="gbb-padding-small">Some ISPs or internet access points share a single IP address among their users and so this may just be multiple users on the same ISP / access point</li>
                                    <li class="gbb-padding-small">Some ISPs expire their IP address assignments and then re-assign them to different users, and so again, it may just be multiple users with the same ISP</li>
                                    <li class="gbb-padding-small">Some users use a publicly accessible proxy to protect their identity and therefore would have the same IP address</li>
                                    <li class="gbb-padding-small">It's possible, though rare, that someone could be spoofing their IP address to impersonate another user or hide their identity</li>
                                </ul>
                                
                                So BEFORE YOU DO ANYTHING, like banning or messaging this user, please use some common sense and compare this user's writing style to the posts listed below.
                                
                                <br /><br />
                                
                                The 5 most recent topics and replies, that were posted from this same IP address, are listed below. 
                                If they appear to be similar in content and biases, and you have a policy against users posting from multiple accounts, then please proceed with the appropriate action for your board.
                                
                                <br /><br />
                                
                                <div id="modal-moderate-reply-duplicate-topic-head" class="uk-grid">
                                    <div class="uk-width-2-10"><h5>User</h5></div>
                                    <div class="uk-width-8-10"><h5>Topic</h5></div>
                                </div>
                                
                                <div id="modal-moderate-reply-duplicate-topic-rows" class="uk-grid gbb-spacing">
                                </div>
                                
                                <div id="modal-moderate-reply-duplicate-reply-head" class="uk-grid gbb-spacing">
                                    <div class="uk-width-2-10"><h5>User</h5></div>
                                    <div class="uk-width-8-10"><h5>Reply</h5></div>
                                </div>
                                
                                <div id="modal-moderate-reply-duplicate-reply-rows" class="uk-grid gbb-spacing">
                                </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                
                <br />
                
                <div class="uk-width-1-1 uk-text-right">
                    <a id="modal-moderate-reply-reload" class="uk-button uk-button-primary">Close & Reload</a>
                    <a class="uk-button uk-button-primary uk-modal-close">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>