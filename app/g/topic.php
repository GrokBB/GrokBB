<?php
if (!isset($_SESSION['user'])) {
    header('Location: ' . SITE_BASE_URL);
}

$title = '';
$reply = false;
$replyDash = strpos($_SESSION['objectid'], '-');

if ($replyDash !== false) {
    $replyID = (int) substr($_SESSION['objectid'], $replyDash + 1);
    
    // redirect if the topic already exists
    $topic = $GLOBALS['db']->getOne('topic', array('id_ry' => $replyID));
    
    if ($topic) {
        header('Location: ' . SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/' . $topic->id);
    }
    
    // locate the existing reply this topic was started from
    $reply = $GLOBALS['db']->getOne('reply r INNER JOIN ' . DB_PREFIX . 'user u ON r.id_ur = u.id INNER JOIN ' . DB_PREFIX . 'topic t ON r.id_tc = t.id INNER JOIN ' . 
        DB_PREFIX . 'board_category bc ON t.id_bc = bc.id', array('r.id' => $replyID), false, array('r.*', 'u.username', 't.title', 't.id_bc', 'bc.private'));
    
    if (!$reply) {
        header('Location: ' . SITE_BASE_URL);
    }
    
    $title = 'RE: ' . $reply->title . ' / ' . $reply->username;
}

$GLOBALS['includeEditor'] = true;

require(SITE_BASE_APP . 'header.php');

$where = array('id_bd' => $board->id, 'private' => 0);

if ($_SESSION['user']->isModerator == $board->id) {
    unset($where['private']);
}

$categories = $GLOBALS['db']->getAll('board_category', $where, 'defcat DESC, name ASC');

if ($board->topic_request_access == '') {
    $board->topic_request_access = 'This is a moderated board, and only approved users are allowed to post topics. Please enter your reason for requesting access below.';
}
?>

<input type="hidden" id="topic-reply" value="<?php echo ($reply) ? $reply->id : 0; ?>" />

<div class="uk-grid uk-grid-small" data-uk-grid-margin>
    <div class="uk-width-small-1-1 uk-width-large-3-4">
        <?php if ($board->type == 2 && \GrokBB\Board::isApproved($board->id) == false) { ?>
        <div class="uk-panel uk-panel-box uk-panel-header">
            <div class="uk-panel-title">
                <span class="uk-text-bold uk-text-primary">Request For Access</span>
                <button id="request-access-submit" class="uk-button uk-button-primary uk-align-right gbb-editor-button">Submit Request</button>
                <div class="uk-text-small uk-align-right gbb-editor-characters"><span id="editor-request-access-char">0</span> / 15,000 characters</div>
            </div>
            
            <div><?php echo $board->topic_request_access; ?></div><br />
            
            <textarea id="editor-request-access"></textarea>
        </div>
        <?php } else { ?>
        <ul class="uk-tab uk-tab-grid" data-uk-tab="{ connect: '#topic-tabs' }">
            <li class="uk-width-2-10" id="topic-create"><a href="#"><span class="uk-hidden-xsmall">Create Topic</span><span class="uk-visible-xsmall">Topic</span></a></li>
            <?php if (0 /* $board->topic_allowpolls */) { ?>
            <li class="uk-width-2-10" id="topic-poll"><a href="#">Include a Poll</a></li>
            <?php } ?>
            <?php if ($_SESSION['user']->isModerator == $board->id) { ?>
            <li class="uk-width-2-10" id="topic-settings"><a href="#"><span class="uk-hidden-small">Optional Settings</span><span class="uk-visible-small">Optional</span><span class="uk-visible-xsmall">Optional</span></a></li>
            <?php } ?>
        </ul>
        
        <ul id="topic-tabs" class="uk-switcher">
            <li class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title">
                    <span class="uk-text-bold uk-text-primary">Create Topic</span>
                    <button id="editor-create" class="uk-button uk-button-primary uk-align-right gbb-editor-button">Create</button>
                    <div class="uk-text-small uk-align-right gbb-editor-characters"><span id="editor-char">0</span> / 15,000 characters</div>
                </div>
                
                <form class="uk-form uk-form-stacked">
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="topic-title">Title</label>
                        <div class="uk-form-controls">
                            <input class="uk-width-1-2" type="text" id="topic-title" maxlength="120" value="<?php echo $title; ?>">
                            &nbsp;<span id="topic-create-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                        </div>
                    </div>
                    
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="topic-category">Category</label>
                        <div class="uk-grid uk-grid-small gbb-padding-large">
                            <?php foreach ($categories as $category) { ?>
                            <div class="uk-width-small-1-2 uk-width-medium-1-3 uk-width-xlarge-1-4">
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td><input id="topic-category-<?php echo $category->id; ?>" name="topic-category" value="<?php echo $category->id; ?>" type="radio" <?php echo (((!$reply || ($reply->private && $_SESSION['user']->isModerator == false)) && $category->defcat) || ($reply && $reply->id_bc == $category->id)) ? 'checked="checked"' : ''; ?> /></td>
                                    <td id="topic-category-box-<?php echo $category->id; ?>">
                                        <?php if ($category->image) { ?>
                                        <img src="<?php echo SITE_BASE_URL . '/img.php?bid=' . $board->id . '&cat=' . $category->id; ?>" width="190" height="40" data-uk-tooltip="{ pos: 'top-right' }" title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>">
                                        <?php } else { ?>
                                        <figure class="uk-overlay" style="background-color: <?php echo $category->color; ?>">
                                            <img src="<?php echo SITE_BASE_URL . '/img/category.png'; ?>" width="190" height="40" title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>">
                                            <figcaption class="uk-overlay-panel uk-flex uk-flex-middle uk-overlay-scale"><?php echo $category->name; ?></figcaption>
                                        </figure>
                                        <?php } ?>
                                    </td>
                                </tr>
                                </table>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="editor-text"><?php echo $board->topic_content_name; ?></label>
                        <span class="uk-text-small"><?php echo $board->topic_content_desc; ?></span>
                        <textarea id="editor-text"></textarea>
                    </div>
                    
                    <?php if ($reply) { ?>
                    <div class="uk-form-row">
                        <label class="uk-form-label">
                            Replying To <a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/' . $reply->id_tc . '#open' . $reply->id_ry . 'reply' . $reply->id; ?>"><?php echo $reply->title; ?></a>
                            &nbsp;/&nbsp;
                            Response By <a href="<?php echo SITE_BASE_URL . '/user/view/' . $reply->id_ur; ?>"><?php echo $reply->username; ?></a>
                        </label>
                        <div id="topic-replyHTML"><?php echo $reply->content; ?></div>
                    </div>
                    <?php } ?>
                    
                    <div id="topic-media" class="uk-form-row">
                        <label class="uk-form-label">Your Media</label>
                        <span class="uk-text-small">
                            If your topic refers to a picture, video or audio file on another site then you can add a link to it here, and it will automatically display under your content. If more than one link is added then a slideshow will be available. 
                            You can link to any browser supported image type or the following audio / video formats: YouTube, Vimeo, MP4, WebM, OGV, MP3, Wave, OGG
                        </span>
                        <div id="topic-media-all" class="uk-grid uk-grid-small">
                            <div class="uk-width-8-10 gbb-spacing topic-media-div">
                                <input class="uk-width-1-1 topic-media-url" type="text" maxlength="2000" value="">
                                <div class="uk-margin-top uk-margin-bottom">
                                    <strong>Caption</strong>&nbsp;&nbsp;<input class="uk-width-1-2 topic-media-txt" type="text" maxlength="120" value="">
                                </div>
                            </div>
                            <div class="uk-width-2-10 gbb-spacing">
                                <a class="uk-button uk-button-primary topic-media-add"><span class="uk-hidden-xsmall">Add New Link</span><span class="uk-visible-xsmall">Add</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </li>
            
            <?php if (0 /* $board->topic_allowpolls */) { ?>
            <li class="uk-panel uk-panel-box uk-panel-header">
                POLL
            </li>
            <?php } ?>
            
            <?php if ($_SESSION['user']->isModerator == $board->id) { ?>
            <li class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title">
                    <span class="uk-text-bold uk-text-primary">Optional Settings</span>
                </div>
                
                <div><input type="checkbox" id="topic-sticky" />&nbsp;make topic sticky (always appears at the top)</div>
                <div class="gbb-spacing"><input type="checkbox" id="topic-private" />&nbsp;make topic viewable to moderators only</div>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>
    </div>
    <?php require('sidebar.php'); ?>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>