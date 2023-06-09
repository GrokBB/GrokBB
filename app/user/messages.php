<?php
if (!isset($_SESSION['user'])) {
    header('Location: ' . SITE_BASE_URL);
}

$GLOBALS['includeEditor'] = true;

require(SITE_BASE_APP . 'header.php');

$messagesPerPage = 50;

$announcementsRawDB = $GLOBALS['db']->getAll('board_announcement ba ' .
    'INNER JOIN ' . DB_PREFIX . 'user_board ub ON ba.id_bd = ub.id_bd INNER JOIN ' . DB_PREFIX . 'board b ON ba.id_bd = b.id ' . 
    ' LEFT JOIN ' . DB_PREFIX . 'user_board_announcement uba ON ba.id = uba.id_ba AND uba.id_ur = ' . $_SESSION['user']->id, 
    array('ub.id_ur' => $_SESSION['user']->id, 'ub.deleted' => 0, 'ba.sent' => array('> ub.added')), 'ba.sent DESC', array('ba.*', 'uba.read', 'b.name'));

$announcements = array();
$announcementsPages = 1;
$announcementsCount = 0;

foreach ($announcementsRawDB as $announcement) {
    if ($announcementsCount > 0) {
        if ($announcementsCount % $messagesPerPage == 0) {
            $announcementsPages++;
        }
    }
    
    $announcements[$announcementsPages][] = $announcement;
    $announcementsCount++;
}

$messages = $GLOBALS['db']->getAll('message m INNER JOIN ' . DB_PREFIX . 'user u ON m.id_ur = u.id', 
    array('m.id_to' => $_SESSION['user']->id, 'm.deleted' => 0, 'm.rcvd' => array('>', 0)), 'm.rcvd DESC', array('m.*', 'u.username'));

$messagesInbox = array();
$messagesInboxPages = 1;
$messagesInboxCount = 0;

foreach ($messages as $message) {
    if ($messagesInboxCount > 0) {
        if ($messagesInboxCount % $messagesPerPage == 0) {
            $messagesInboxPages++;
        }
    }
    
    $messagesInbox[$messagesInboxPages][] = $message;
    $messagesInboxCount++;
}

$messages = $GLOBALS['db']->getAll('message m INNER JOIN ' . DB_PREFIX . 'user u ON m.id_to = u.id', 
    array('m.id_ur' => $_SESSION['user']->id, 'm.deleted' => 0, 'm.rcvd' => 0), 'm.sent DESC, m.updated DESC', array('m.*', 'u.username'));

$messagesSent = array();
$messagesSentPages = 1;
$messagesSentCount = 0;

$messagesDrft = array();
$messagesDrftPages = 1;
$messagesDrftCount = 0;

foreach ($messages as $message) {
    if ($messagesSentCount > 0) {
        if ($messagesSentCount % $messagesPerPage == 0) {
            $messagesSentPages++;
        }
    }
    
    if ($messagesDrftCount > 0) {
        if ($messagesDrftCount % $messagesPerPage == 0) {
            $messagesDrftPages++;
        }
    }
    
    if ($message->sent > 0) {
        $messagesSent[$messagesSentPages][] = $message;
        $messagesSentCount++;
    } else {
        $messagesDrft[$messagesDrftPages][] = $message;
        $messagesDrftCount++;
    }
}
?>

<ul class="uk-tab uk-tab-grid" data-uk-tab="{ connect: '#message-tabs' }">
    <li class="uk-width-1-5" id="announce-boards"><a href="#"><span class="uk-visible-large">Board Announcements</span><span class="uk-hidden-xsmall uk-hidden-large">Announcements</span><span class="uk-visible-xsmall">Ann.</span></a></li>
    <li class="uk-width-1-5" id="messages-inbox"><a href="#"><span class="uk-visible-large">Message Inbox</span><span class="uk-hidden-large">Inbox</span></a></li>
    <li class="uk-width-1-5" id="messages-create"><a href="#"><span class="uk-visible-large">Create New Message</span><span class="uk-hidden-large">New<span class="uk-hidden-xsmall"> Message</span></span></a></li>
    <li class="uk-width-1-5" id="messages-drafts"><a href="#"><span class="uk-hidden-xsmall">Saved </span>Drafts</a></li>
    <li class="uk-width-1-5" id="messages-sent"><a href="#"><span class="uk-visible-large">Sent Messages</span><span class="uk-hidden-large">Sent</span></a></li>
</ul>

<ul id="message-tabs" class="uk-switcher">
    <li class="uk-panel uk-panel-box">
        <?php if (count($announcements) > 0) { ?>
        <ul id="messages-pagination-announcements" class="uk-pagination">
            <li id="messages-prev-announcements" class="uk-disabled"><a href="#"><i class="uk-icon-angle-double-left"></i></a></li>
            <?php for ($i = 1; $i <= $announcementsPages; $i++) { ?>
            <li id="messages-goto-announcements-<?php echo $i; ?>"<?php echo ($i == 1) ? ' class="uk-active"' : ''; ?>><a href="#"><?php echo $i; ?></a></li>
            <?php } ?>
            <li id="messages-next-announcements"><a href="#"><i class="uk-icon-angle-double-right"></i></a></li>
        </ul>
        
        <br />
        
        <?php for ($i = 1; $i <= $announcementsPages; $i++) { ?>
        <div id="messages-page-announcements-<?php echo $i; ?>" <?php echo ($i > 1) ? ' style="display: none"' : ''; ?>>
            <table class="uk-table uk-table-striped">
                <tr>
                    <th>Board</th>
                    <th>Subject</th>
                    <th><i class="uk-icon-caret-square-o-down"></i>&nbsp;&nbsp;Received</th>
                </tr>
                <?php foreach ($announcements[$i] as $announcement) { ?>
                <tr id="announcements-row-<?php echo $announcement->id; ?>">
                    <td nowrap="nowrap"><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $announcement->name); ?>" data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to Visit Board"><?php echo $announcement->name; ?></a></td>
                    <td id="announcements-subject-<?php echo $announcement->id; ?>" class="<?php echo ($announcement->read > 0) ? ' uk-text-muted' : ''; ?>" data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to View Announcement"><i id="announcements-icon-<?php echo $announcement->id; ?>" class="uk-icon-plus-square"></i>&nbsp;&nbsp;<?php echo $announcement->subject; ?></td>
                    <td><?php echo GrokBB\Util::getTimespan($announcement->sent, 3); ?> ago</td>
                </tr>
                <tr id="announcements-view-<?php echo $announcement->id; ?>" style="display: none">
                    <td colspan="4"><div id="announcements-content-<?php echo $announcement->id; ?>"></div></td>
                </tr>
                <!-- only here to maintain correct striping -->
                <tr style="display: none"><td></td></tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php } else { ?>
        <div class="uk-alert uk-alert-info" data-uk-alert>
            There are no board announcements.
        </div>
        <?php } ?>
    </li>
    
    <li class="uk-panel uk-panel-box">
        <?php if (count($messagesInbox) > 0) { ?>
        <ul id="messages-pagination-inbox" class="uk-pagination">
            <li id="messages-prev-inbox" class="uk-disabled"><a href="#"><i class="uk-icon-angle-double-left"></i></a></li>
            <?php for ($i = 1; $i <= $messagesInboxPages; $i++) { ?>
            <li id="messages-goto-inbox-<?php echo $i; ?>"<?php echo ($i == 1) ? ' class="uk-active"' : ''; ?>><a href="#"><?php echo $i; ?></a></li>
            <?php } ?>
            <li id="messages-next-inbox"><a href="#"><i class="uk-icon-angle-double-right"></i></a></li>
        </ul>
        
        <br />
        
        <?php for ($i = 1; $i <= $messagesInboxPages; $i++) { ?>
        <div id="messages-page-inbox-<?php echo $i; ?>" <?php echo ($i > 1) ? ' style="display: none"' : ''; ?>>
            <table class="uk-table uk-table-striped">
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th class="uk-hidden-xsmall" nowrap="nowrap"><i class="uk-icon-caret-square-o-down"></i>&nbsp;&nbsp;Received</th>
                    <th class="uk-text-center uk-hidden-xsmall">Actions</th>
                </tr>
                <?php foreach ($messagesInbox[$i] as $message) { ?>
                <tr id="messages-row-<?php echo $message->id; ?>">
                    <td class="gbb-editor-characters" nowrap="nowrap"><a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $message->id_to; ?>" data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to View User"><?php echo $message->username; ?></a></td>
                    <td class="gbb-editor-characters<?php echo ($message->read > 0) ? ' uk-text-muted' : ''; ?>" id="messages-subject-<?php echo $message->id; ?>" data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to View Message"><i id="messages-icon-<?php echo $message->id; ?>" class="uk-icon-plus-square"></i>&nbsp;&nbsp;<?php echo $message->subject; ?></td>
                    <td class="gbb-editor-characters uk-hidden-xsmall"><div class="uk-visible-large"><?php echo GrokBB\Util::getTimespan($message->rcvd, 3); ?> ago</div><div class="uk-hidden-large"><?php echo GrokBB\Util::getTimespan($message->rcvd, 1); ?> ago</div></td>
                    <td id="messages-actions-inbox-<?php echo $message->id; ?>" class="uk-text-center uk-hidden-xsmall" nowrap="nowrap">
                        <button id="messages-verify-inbox-<?php echo $message->id; ?>" class="uk-button uk-button-danger" style="display: none">Confirm Delete</button>
                        <button id="messages-cancel-inbox-<?php echo $message->id; ?>" class="uk-button uk-button-primary message-button" style="display: none">Cancel</button>
                        <button id="messages-delete-inbox-<?php echo $message->id; ?>" class="uk-button uk-button-primary">Delete</button>
                        <button id="messages-reply-inbox-<?php echo $message->id; ?>" class="uk-button uk-button-primary message-button" <?php echo ($message->id_ry > 0 || $message->id_tc > 0) ? 'disabled="disabled"' : ''; ?>>Reply</button>
                    </td>
                </tr>
                <tr id="messages-view-<?php echo $message->id; ?>" style="display: none">
                    <td colspan="4"><div id="messages-content-<?php echo $message->id; ?>"></div></td>
                </tr>
                <!-- only here to maintain correct striping -->
                <tr style="display: none"><td></td></tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php } else { ?>
        <div class="uk-alert uk-alert-info" data-uk-alert>
            You have no new messages.
        </div>
        <?php } ?>
    </li>
    
    <li class="uk-panel uk-panel-box uk-panel-header">
        <div class="uk-panel-title">
            <span class="uk-text-bold uk-text-primary">Create New Message</span>
            <span class="uk-visible-xsmall"><br /><br /></span>
            <button id="editor-save" class="uk-button uk-button-primary uk-align-right gbb-editor-button">Save Draft</button>
            <button id="editor-send" class="uk-button uk-button-primary uk-align-right gbb-editor-button">Send Message</button>
            <div class="uk-hidden-xsmall uk-text-small uk-align-right gbb-editor-characters"><span id="editor-char">0</span> / 15,000 characters</div>
            <span class="uk-visible-xsmall"><br /></span>
        </div>
        
        <form class="uk-form uk-form-stacked">
            <input type="hidden" id="message-id" value="0">
            <input type="hidden" id="message-sent" value="0">
            
            <div class="uk-form-row">
                <label class="uk-form-label" for="message-to">To</label>
                <div class="uk-form-controls">
                    <input class="uk-form-width-medium" type="text" placeholder="" id="message-to" maxlength="15">
                    &nbsp;<span id="message-to-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                </div>
            </div>
            <div class="uk-form-row">
                <label class="uk-form-label" for="message-subject">Subject</label>
                <div class="uk-form-controls">
                    <input class="uk-width-100" type="text" placeholder="" id="message-subject" maxlength="60">
                </div>
            </div>
            <div class="uk-form-row">
                <label class="uk-form-label" for="editor-text">Message</label>
                <textarea id="editor-text"></textarea>
            </div>
            
            <div class="uk-form-row" id="message-replyArea" style="display: none">
                <label class="uk-form-label">Replying To</label>
                <div id="message-replyHTML"></div>
            </div>
        </form>
    </li>
    
    <li class="uk-panel uk-panel-box">
        <?php if (count($messagesDrft) > 0) { ?>
        <ul id="messages-pagination-drft" class="uk-pagination">
            <li id="messages-prev-drft" class="uk-disabled"><a href="#"><i class="uk-icon-angle-double-left"></i></a></li>
            <?php for ($i = 1; $i <= $messagesDrftPages; $i++) { ?>
            <li id="messages-goto-drft-<?php echo $i; ?>"<?php echo ($i == 1) ? ' class="uk-active"' : ''; ?>><a href="#"><?php echo $i; ?></a></li>
            <?php } ?>
            <li id="messages-next-drft"><a href="#"><i class="uk-icon-angle-double-right"></i></a></li>
        </ul>
        
        <br />
        
        <?php for ($i = 1; $i <= $messagesDrftPages; $i++) { ?>
        <div id="messages-page-drft-<?php echo $i; ?>" <?php echo ($i > 1) ? ' style="display: none"' : ''; ?>>
            <table class="uk-table uk-table-striped">
                <tr>
                    <th>To</th>
                    <th>Subject</th>
                    <th class="uk-hidden-xsmall" nowrap="nowrap"><i class="uk-icon-caret-square-o-down"></i>&nbsp;&nbsp;Last Updated</th>
                    <th class="uk-text-center uk-hidden-xsmall">Actions</th>
                </tr>
                <?php foreach ($messagesDrft[$i] as $message) { ?>
                <tr id="messages-row-<?php echo $message->id; ?>">
                    <td class="gbb-editor-characters" nowrap="nowrap"><a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $message->id_to; ?>" data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to View User"><?php echo $message->username; ?></a></td>
                    <td class="gbb-editor-characters" id="messages-subject-<?php echo $message->id; ?>" data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to View Message"><i id="messages-icon-<?php echo $message->id; ?>" class="uk-icon-plus-square"></i>&nbsp;&nbsp;<?php echo $message->subject; ?></td>
                    <td class="gbb-editor-characters uk-hidden-xsmall"><div class="uk-visible-large"><?php echo GrokBB\Util::getTimespan($message->updated, 3); ?> ago</div><div class="uk-hidden-large"><?php echo GrokBB\Util::getTimespan($message->updated, 1); ?> ago</div></td>
                    <td id="messages-actions-drafts-<?php echo $message->id; ?>" class="uk-text-center uk-hidden-xsmall" nowrap="nowrap">
                        <button id="messages-verify-drafts-<?php echo $message->id; ?>" class="uk-button uk-button-danger" style="display: none">Confirm Delete</button>
                        <button id="messages-cancel-drafts-<?php echo $message->id; ?>" class="uk-button uk-button-primary message-button" style="display: none">Cancel</button>
                        <button id="messages-delete-drafts-<?php echo $message->id; ?>" class="uk-button uk-button-primary">Delete</button>
                        <button id="messages-edit-drafts-<?php echo $message->id; ?>" class="uk-button uk-button-primary message-button">Edit</button>
                    </td>
                </tr>
                <tr id="messages-view-<?php echo $message->id; ?>" style="display: none">
                    <td colspan="4"><div id="messages-content-<?php echo $message->id; ?>"></div></td>
                </tr>
                <!-- only here to maintain correct striping -->
                <tr style="display: none"><td></td></tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php } else { ?>
        <div class="uk-alert uk-alert-info" data-uk-alert>
            You have no saved drafts.
        </div>
        <?php } ?>
    </li>
    
    <li class="uk-panel uk-panel-box">
        <?php if (count($messagesSent) > 0) { ?>
        <ul id="messages-pagination-sent" class="uk-pagination">
            <li id="messages-prev-sent" class="uk-disabled"><a href="#"><i class="uk-icon-angle-double-left"></i></a></li>
            <?php for ($i = 1; $i <= $messagesSentPages; $i++) { ?>
            <li id="messages-goto-sent-<?php echo $i; ?>"<?php echo ($i == 1) ? ' class="uk-active"' : ''; ?>><a href="#"><?php echo $i; ?></a></li>
            <?php } ?>
            <li id="messages-next-sent"><a href="#"><i class="uk-icon-angle-double-right"></i></a></li>
        </ul>
        
        <br />
        
        <?php for ($i = 1; $i <= $messagesSentPages; $i++) { ?>
        <div id="messages-page-sent-<?php echo $i; ?>" <?php echo ($i > 1) ? ' style="display: none"' : ''; ?>>
            <table class="uk-table uk-table-striped">
                <tr>
                    <th>To</th>
                    <th>Subject</th>
                    <th nowrap="nowrap"><i class="uk-icon-caret-square-o-down"></i>&nbsp;&nbsp;Sent</th>
                    <th class="uk-hidden-xsmall" nowrap="nowrap">Read By Recipient</th>
                </tr>
                <?php foreach ($messagesSent[$i] as $message) { ?>
                <tr id="messages-row-<?php echo $message->id; ?>">
                    <td nowrap="nowrap"><a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $message->id_to; ?>" data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to View User"><?php echo $message->username; ?></a></td>
                    <td id="messages-subject-<?php echo $message->id; ?>" data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to View Message"><i id="messages-icon-<?php echo $message->id; ?>" class="uk-icon-plus-square"></i>&nbsp;&nbsp;<?php echo $message->subject; ?></td>
                    <td><div class="uk-visible-large"><?php echo GrokBB\Util::getTimespan($message->sent, 2); ?> ago</div><div class="uk-hidden-large"><?php echo GrokBB\Util::getTimespan($message->sent, 1); ?> ago</div></td>
                    <td class="uk-hidden-xsmall"><div class="uk-visible-large"><?php echo GrokBB\Util::getTimespan($message->read, 2) . (($message->read) ? ' ago': ''); ?></div><div class="uk-hidden-large"><?php echo GrokBB\Util::getTimespan($message->read, 1) . (($message->read) ? ' ago': ''); ?></div></td>
                </tr>
                <tr id="messages-view-<?php echo $message->id; ?>" style="display: none">
                    <td colspan="4"><div id="messages-content-<?php echo $message->id; ?>"></div></td>
                </tr>
                <!-- only here to maintain correct striping -->
                <tr style="display: none"><td></td></tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php } else { ?>
        <div class="uk-alert uk-alert-info" data-uk-alert>
            You have no sent messages.
        </div>
        <?php } ?>
    </li>
</ul>

<?php require(SITE_BASE_APP . 'footer.php'); ?>