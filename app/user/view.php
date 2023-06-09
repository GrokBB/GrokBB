<?php
$user = $GLOBALS['db']->getOne('user u LEFT JOIN ' . DB_PREFIX . 'topic_view tv ON u.id = tv.id_ur AND tv.viewed >= UNIX_TIMESTAMP() - 1440', 
    array('u.id' => (int) $_SESSION['objectid']), false, array('u.*', 'tv.id as online'));

if ($user === false) {
    header('Location: ' . SITE_BASE_URL . '/error/template');
}

require(SITE_BASE_APP . 'header.php');

$uStats = new stdClass();
$uStats->users = array();

$statistics = $GLOBALS['db']->getAll('user', array('id' => $user->id), false, array('id', 'counter_topics', 'counter_replies'));
foreach ($statistics as $stats) { $uStats->users[$stats->id] = $stats; }

if (isset($_SESSION['user'])) {
    $userFriend = $GLOBALS['db']->getOne('user_friend', array('id_ur' => $_SESSION['user']->id, 'id_fd' => $user->id));
    $userFavs = $GLOBALS['db']->getAll('user_board', array('id_ur' => $user->id, 'deleted' => 0));
} else {
    $userFriend = false;
}
?>

<div id="user-profile" class="gbb-backdrop" <?php echo ($user->bio) ? 'style="padding-bottom: 25px"' : ''; ?>>
    <div class="uk-grid">
        <div class="uk-width-xsmall-1-1 uk-width-medium-3-4 uk-width-large-2-3 uk-width-xlarge-1-2 uk-container-center">
            <h2 <?php echo (!$user->bio) ? 'style="margin-bottom: 0px"' : ''; ?>>
                <table cellpadding="0" cellspacing="0">
                <tr>
                    <td><img class="uk-thumbnail" width="60" height="60" src="<?php echo SITE_BASE_URL; ?>/img.php?uid=<?php echo $user->id; ?>" /></td>
                    <td valign="middle">
                        <div class="uk-margin-left">
                            <?php echo $user->username; ?>
                            <div class="uk-text-small">Status:&nbsp;<?php echo ($user->online ) ? '<span class="uk-text-danger">Online</span>' : '<span class="uk-text-muted">Away</span>'; ?></div>
                        </div>
                    </td>
                </tr>
                </table>
            </h2>
            <div class="uk-grid uk-grid-small uk-text-small <?php echo ($user->bio) ? '' : 'gbb-spacing-large'; ?>" data-uk-grid-margin>
                <div class="uk-width-1-1">
                    <strong>Last Login:</strong>
                    <span class="uk-hidden-xsmall">&nbsp;<?php echo GrokBB\Util::getTimespan($user->login, 3) . (($user->login) ? ' ago' : ''); ?></span>
                    <span class="uk-visible-xsmall">&nbsp;<?php echo GrokBB\Util::getTimespan($user->login, 1) . (($user->login) ? ' ago' : ''); ?></span>
                    
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    
                    <strong>Joined:</strong>
                    <span class="uk-hidden-xsmall">&nbsp;<?php echo GrokBB\Util::getTimespan($user->joined, 3); ?> ago</span>
                    <span class="uk-visible-xsmall">&nbsp;<?php echo GrokBB\Util::getTimespan($user->joined, 1); ?> ago</span>
                </div>
            </div>
            
            <hr class="uk-grid-divider uk-margin-remove gbb-spacing">
            
            <div class="uk-grid uk-text-small uk-grid-divider gbb-spacing">
                <div class="uk-width-1">
                    <strong><span class="uk-hidden-xsmall">GrokBB </span>Rep:</strong>
                    &nbsp;<?php echo GrokBB\User::reputation($user->id, $uStats); ?>
                
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    
                    <strong><span class="uk-hidden-xsmall">Total </span>Topics:</strong>
                    &nbsp;<?php echo $user->counter_topics; ?>
                
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    
                    <strong><span class="uk-hidden-xsmall">Total </span>Replies:</strong>
                    &nbsp;<?php echo $user->counter_replies; ?>
                
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    
                    <strong><span class="uk-hidden-xsmall">Total </span>Favorites:</strong>
                    &nbsp;<?php echo ($userFavs) ? count($userFavs) : 0; ?>
                </div>
            </div>
            
            <hr class="uk-grid-divider uk-margin-remove gbb-spacing gbb-padding">
            
            <?php if ($user->bio) { ?>
            <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                <?php echo $user->bio; ?>
            </div>
            <?php } ?>
            <?php if (!isset($_SESSION['user']) || $_SESSION['user']->id != $user->id) { ?>
            <button id="user-send-message" class="uk-button uk-button-primary uk-align-right gbb-spacing-large">Send Message</button>
            <button id="user-friend-add" class="uk-button uk-button-primary uk-align-right gbb-spacing-large" style="<?php echo ($userFriend) ? 'display: none' : ''; ?>">Add Friend</button>
            <button id="user-friend-rem" class="uk-button uk-button-primary uk-align-right gbb-spacing-large" style="<?php echo ($userFriend) ? '' : 'display: none'; ?>">Remove Friend</button>
            <?php } ?>
        </div>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>