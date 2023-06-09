<?php
if (!isset($_SESSION['user'])) {
    header('Location: ' . SITE_BASE_URL);
}

require(SITE_BASE_APP . 'header.php');

$sort = (isset($_GET['sort'])) ? $_GET['sort'] : '';
if (!in_array($sort, array('dsaved', 'bdname', 'tcname', 'urname'))) { $sort = 'dsaved'; }

$orderBy = 'ur.saved DESC, r.created DESC';

switch ($sort) {
    case 'bdname' :
        $orderBy = 'b.name ASC, ' . $orderBy;
        break;
    case 'tcname' :
        $orderBy = 't.title ASC, ' . $orderBy;
        break;
    case 'urname' :
        $orderBy = 'u.username ASC, ' . $orderBy;
        break;
}

$replies = $GLOBALS['db']->getAll('reply r INNER JOIN ' . DB_PREFIX . 'topic t ON r.id_tc = t.id INNER JOIN ' . DB_PREFIX . 'user u ON r.id_ur = u.id INNER JOIN ' . DB_PREFIX . 'board b ON t.id_bd = b.id LEFT JOIN ' . DB_PREFIX . 'user_reply ur ON r.id = ur.id_ry AND r.deleted = 0', 
    array('r.id_ur,ur.id_ur' => $_SESSION['user']->id), $orderBy, array('DISTINCT(r.id) as rid', 'r.*', 'u.*', 't.id as tid', 't.title', 'u.id as uid', 'IF (ur.id, ur.saved, r.created) as created', 'b.name as bdname'));
?>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-badge uk-badge uk-badge-success"><?php echo count($replies); ?></div>
    <h3 class="uk-panel-title uk-text-primary uk-text-bold">My Replies</h3>
    
    <div class="uk-grid" data-uk-grid-margin>
        <?php if ($replies) { ?>
        <div class="uk-width-small-7-10 uk-width-medium-6-10 uk-width-large-5-10 uk-width-xlarge-3-10 uk-align-center uk-margin-bottom-remove gbb-spacing">
            <span class="uk-text-small uk-text-bold uk-hidden-xsmall">Sort Replies By</span>&nbsp;
            <a href="<?php echo SITE_BASE_URL . '/user/replies/?sort=dsaved'; ?>" class="uk-button uk-button-small gbb-button-static <?php echo ($sort == 'dsaved') ? 'uk-button-primary' : ''; ?>"><span class="uk-hidden-xsmall">Date </span>Saved</a>&nbsp;
            <a href="<?php echo SITE_BASE_URL . '/user/replies/?sort=bdname'; ?>" class="uk-button uk-button-small gbb-button-static <?php echo ($sort == 'bdname') ? 'uk-button-primary' : ''; ?>">Board Name</a>&nbsp;
            <a href="<?php echo SITE_BASE_URL . '/user/replies/?sort=tcname'; ?>" class="uk-button uk-button-small gbb-button-static <?php echo ($sort == 'tcname') ? 'uk-button-primary' : ''; ?>">Topic</a>&nbsp;
            <a href="<?php echo SITE_BASE_URL . '/user/replies/?sort=urname'; ?>" class="uk-button uk-button-small gbb-button-static <?php echo ($sort == 'urname') ? 'uk-button-primary' : ''; ?>">Username</a>
        </div>
        <?php foreach ($replies as $reply) { ?>
        <div id="reply-<?php echo $reply->rid; ?>" class="uk-width-xsmall-1-1 uk-width-small-3-4 uk-align-center uk-margin-bottom-remove">
            <div class="uk-panel uk-panel-box uk-padding-remove">
                <div class="uk-grid uk-grid-collapse reply-padding">
                    <div class="uk-panel-badge reply-padding">
                        <div class="uk-text-small uk-float-right uk-text-bold reply-saved">
                            <?php if ($_SESSION['user']->id != $reply->id_ur) { ?>
                            <i id="reply-unsave-<?php echo $reply->rid; ?>" class="uk-icon uk-icon-remove uk-align-right" data-uk-tooltip="{ pos: 'top-right' }" title="Unsave Reply"></i>
                            <?php } ?>
                            <span class="uk-hidden-xsmall uk-hidden-small uk-hidden-medium">Saved <?php echo GrokBB\Util::getTimespan($reply->created, 2); ?> ago</span>
                            <span class="uk-hidden-large">Saved <?php echo GrokBB\Util::getTimespan($reply->created, 1); ?> ago</span>
                        </div>
                    </div>
                    
                    <div id="reply-user-<?php echo $reply->id_ur; ?>" class="uk-width-xsmall-2-10 uk-width-small-2-10 uk-width-medium-1-10 uk-text-center gbb-padding reply-user" data-uk-tooltip="{ pos: 'bottom-left' }" title="Joined: <?php echo ltrim(GrokBB\Util::getTimespan($reply->joined, 1), 0); ?> ago<br>Click to View Profile">
                        <div class="uk-container-center">
                            <img src="<?php echo SITE_BASE_URL; ?>/img.php?uid=<?php echo $reply->id_ur; ?>" width="60"><br />
                            <span class="uk-text-small uk-text-bold"><?php echo $reply->username; ?></span>
                        </div>
                    </div>
                    
                    <div class="uk-width-xsmall-8-10 uk-width-small-8-10 uk-width-medium-6-10 uk-width-xlarge-7-10 gbb-padding">
                        <span class="uk-hidden-medium uk-hidden-large"><br /><br /></span>
                        <a href="<?php echo SITE_BASE_URL . '/g/' . str_replace(' ', '_', $reply->bdname); ?>"><?php echo $reply->bdname; ?></a>
                        <span class="reply-info-sep">&nbsp;&raquo;&nbsp;</span>
                        <a href="<?php echo SITE_BASE_URL . '/g/' . str_replace(' ', '_', $reply->bdname) . '/view/' . $reply->tid . '#' . (($reply->id_ry) ? 'open' . $reply->id_ry : '') . 'reply' . $reply->rid; ?>"><?php echo $reply->title; ?></a>
                        
                        <br />
                        
                        <?php echo $reply->content; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php } else { ?>
        <div class="uk-width-1-1">
            <div class="uk-alert uk-alert-info" data-uk-alert>You have no saved replies.</div>
        </div>
        <?php } ?>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>