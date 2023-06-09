<?php
if (!isset($_SESSION['user'])) {
    header('Location: ' . SITE_BASE_URL);
}

require(SITE_BASE_APP . 'header.php');

$friends = $GLOBALS['db']->getAll('user u INNER JOIN ' . DB_PREFIX . 'user_friend uf ON u.id = uf.id_fd LEFT JOIN ' . DB_PREFIX . 'topic_view tv ON u.id = tv.id_ur AND tv.viewed >= UNIX_TIMESTAMP() - 1440', array('uf.id_ur' => $_SESSION['user']->id), 'u.username ASC', array('u.*', 'uf.added', 'tv.viewed as online'), false, false, 'u.id');
?>

<div id="friends" class="uk-panel uk-panel-box">
    <div class="uk-panel-badge uk-badge uk-badge-success"><?php echo count($friends); ?></div>
    <h3 class="uk-panel-title uk-text-primary uk-text-bold">My Friends</h3>
    
    <?php if ($friends) { ?><br /><?php } ?>
    
    <div class="uk-grid" data-uk-grid-margin>
        <?php if ($friends) { ?>
            <?php foreach ($friends as $friend) { ?>
            <div class="uk-width-small-1-2 uk-width-medium-1-2 uk-width-large-1-3 uk-width-xlarge-1-4">
                <h2 id="friend-view-<?php echo $friend->id; ?>" style="margin-bottom: 5px" data-uk-tooltip="{ pos: 'top' }" title="Click to View Profile">
                    <img class="uk-thumbnail" width="60" height="60" src="<?php echo SITE_BASE_URL; ?>/img.php?uid=<?php echo $friend->id; ?>" />
                    &nbsp;<?php echo $friend->username; ?>
                </h2>
                
                <div class="uk-grid">
                    <div class="uk-width-8-10">
                        <span class="uk-text-small">
                            Status:&nbsp;<div class="friend-status"><?php echo ($friend->online ) ? '<span class="uk-text-danger">Online</span>' : '<span class="uk-text-muted">Away</span>'; ?></div>
                            
                            &nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            
                            <span class="uk-text-small friend-added">Added <?php echo ltrim(GrokBB\Util::getTimespan($friend->added, 1), 0); ?> ago</span>
                        </span>
                    </div>
                    <div class="uk-width-1-10">
                        <i id="friend-remove-<?php echo $friend->id; ?>" class="uk-icon uk-icon-remove uk-align-right" data-uk-tooltip="{ pos: 'left' }" title="Remove Friend"></i>
                    </div>
                </div>
            </div>
            <?php } ?>
        <?php } else { ?>
            <div class="uk-width-1-1">
                <div class="uk-alert uk-alert-info" data-uk-alert>
                    <i class="uk-icon-quote-left gbb-icon-small"></i>&nbsp;Truly great friends are hard to find, difficult to leave and impossible to forget.&nbsp;<i class="uk-icon-quote-right gbb-icon-small"></i>&nbsp;&nbsp;<span class="uk-hidden-medium uk-hidden-large"><br /></span>-- Calvin and Hobbes
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>