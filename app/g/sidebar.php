<?php
if ($board->desc_sidebar_mods) {
    $moderators = $GLOBALS['db']->custom('SELECT u.*, ubm.added FROM ' . DB_PREFIX . 'user u LEFT JOIN ' . DB_PREFIX . 'user_board_moderator ubm ON u.id = ubm.id_ur AND ubm.id_bd = ' . $board->id . ' ' .
                                         'WHERE ubm.id IS NOT NULL OR u.id = ' . $board->id_ur . ' ORDER BY u.id = ' . $board->id_ur . ' DESC, ubm.added');
    
    $moderatorsCount = count($moderators);
}

if ($board->desc_sidebar_whos) {
    $whosonline = $GLOBALS['db']->getAll('user u INNER JOIN ' . DB_PREFIX . 'topic_view tv ON u.id = tv.id_ur AND tv.viewed >= UNIX_TIMESTAMP() - 1440 INNER JOIN ' . DB_PREFIX . 'topic t ON tv.id_tc = t.id',
                                         array('t.id_bd' => $board->id), 'u.username', array('u.*'), false, false, 'u.id');
    
    $whosonlineCount = count($whosonline);
}
?>
<div class="uk-hidden-small uk-hidden-medium uk-width-large-1-4">
    <div class="uk-panel uk-panel-box">
        <?php if ($board->type == 1 && \GrokBB\Board::isApproved($board->id) == false) { ?>
        <h3 id="sidebar-title" class="uk-panel-title uk-text-bold uk-margin-remove"><?php echo $board->name; ?><?php if ($board->type != 1) { ?><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/rss"><i id="sidebar-rss" class="uk-icon uk-icon-rss uk-float-right"></i></a><?php } ?></h3>
        <?php } else { ?>
        <h3 id="sidebar-title" class="uk-panel-title uk-text-bold"><?php echo $board->name; ?><?php if ($board->type != 1) { ?><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/rss"><i id="sidebar-rss" class="uk-icon uk-icon-rss uk-float-right"></i></a><?php } ?></h3>
        
        <div id="sidebar-content">
            <?php if (isset($_SESSION['user']) && ($_SESSION['user']->isOwner == $board->id || $_SESSION['user']->isModerator == $board->id)) { ?>
            <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                <?php if ($_SESSION['user']->isOwner == $board->id) { ?>
                <div><a id="sidebar-settings" href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings" class="uk-button uk-button-primary sidebar-button"><span>My Board Settings</span></a></div>
                <div><a id="sidebar-users" href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users" class="uk-button uk-button-primary sidebar-button-small"><span>Users & Stats</span></a></div>
                <?php } else { ?>
                <div><a id="sidebar-moderate" href="<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users" class="uk-button uk-button-primary sidebar-button"><span>Moderate Users</span></a></div>
                <?php } ?>
            </div>
            <hr />
            <?php } ?>
            
            <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                <div><button id="sidebar-newtopic" class="uk-button uk-button-primary sidebar-button" <?php echo ($board->isArchived || (isset($_SESSION['user']) && $_SESSION['user']->isBanned)) ? 'disabled="disabled"' : ''; ?>><span>Create Topic</span><i class="uk-icon-chevron-circle-right"></i></button></div>
                <div><button id="sidebar-favorite" class="uk-button uk-button-primary sidebar-button-small"><span>Favorite</span><i class="uk-icon-star<?php echo ($board->favorite) ? '' : '-o'; ?>"></i></button></div>
            </div>
            <hr />
            
            <div id="sidebar-description"><?php echo $board->desc_sidebar; ?></div>
            
            <div id="sidebar-bottom">
                <?php if ($board->desc_sidebar_mods) { ?>
                <div id="sidebar-moderators">
                    <hr />
                    
                    <strong>Board Moderators</strong>
                    <div class="uk-badge uk-badge-success uk-align-right"><?php echo $moderatorsCount; ?></div>
                    
                    <br />
                    
                    <ul class="uk-list uk-list-line">
                    <?php
                    foreach ($moderators as $moderator) {
                        echo '<li><a href="' . SITE_BASE_URL . '/user/view/' . $moderator->id . '">' . $moderator->username . '</a>' . 
                             (($moderator->id == $board->id_ur) ? '&nbsp;(owner) ' . (($moderatorsCount > 1) ? '<span class="uk-align-right uk-text-bold">Added</span>' : '') : 
                             '<span class="uk-align-right">' . GrokBB\Util::getTimespan($moderator->added, 1) . ' ago</span>') . '</li>';
                    }
                    ?>
                    </ul>
                </div>
                <?php } ?>
                
                <?php if ($board->desc_sidebar_whos) { ?>
                <div id="sidebar-whosonline">
                    <hr />
                    
                    <strong>Who is Online</strong>
                    <div class="uk-badge uk-badge-success uk-align-right"><?php echo $whosonlineCount; ?></div>
                    
                    <br />
                    
                    <ul class="uk-list uk-list-line">
                        <li>
                        <?php
                        if ($whosonlineCount > 0) {
                            $whoCount = 0;
                            foreach ($whosonline as $who) {
                                echo '<a href="' . SITE_BASE_URL . '/user/view/' . $who->id . '">' . $who->username . '</a>';
                                $whoCount++; if ($whoCount < $whosonlineCount) { echo ', '; }
                            }
                        } else {
                            echo '<span class="uk-text-muted">No Users</span>';
                        }
                        ?>
                        </li>
                    </ul>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- modals that need to work in Home and View -->

<div id="modal-share" class="uk-modal">
    <div class="uk-modal-dialog">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Content Sharing & Social Integration</div>
                
                This feature is not implemented yet. It is on the roadmap and will be released soon.<br /><br />
                Don't go! We would love to hear your thoughts and brainstorm ideas with you &nbsp;(•‿•)<br />
                Please particpate in the discussions at <a href="https://www.grokbb.com/g/GrokBB_Dev">GrokBB Dev</a>.
                
                <br /><br />
                
                <div class="uk-width-1-1 uk-text-right">
                    <a class="uk-button uk-button-primary uk-modal-close">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-moderate" class="uk-modal">
    <input type="hidden" id="modal-moderate-id" value="0" />
    <input type="hidden" id="modal-moderate-username" value="" />
    <input type="hidden" id="modal-moderate-userid" value="0" />
    
    <div class="uk-modal-dialog">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Moderate<br /><span id="modal-moderate-title" class="uk-text-small"></span></div>
                
                <ul class="uk-tab uk-tab-grid" data-uk-tab="{ connect: '#moderate-tabs' }">
                    <li class="uk-width-1-3"><a href="#" class="moderate-border">Topic</a></li>
                    <li class="uk-width-1-3"><a href="#" class="moderate-border">User</a></li>
                </ul>
                
                <ul id="moderate-tabs" class="uk-switcher">
                    <li class="uk-panel uk-panel-box uk-panel-box-secondary moderate-border">
                        <input type="checkbox" id="modal-moderate-sticky" />&nbsp;make topic sticky (always appears at the top)<br />
                        <input type="checkbox" id="modal-moderate-private" />&nbsp;make topic viewable to moderators only<br />
                        <input type="checkbox" id="modal-moderate-locked" />&nbsp;make topic locked  / read only<br />
                        
                        <br />
                        
                        <div class="uk-text-bold">Moderator Notes</div>
                        <textarea id="modal-moderate-notes" class="gbb-spacing" style="width: 490px; height: 100px"></textarea>
                        <button id="modal-moderate-notes-update" class="uk-button uk-button-small uk-button-primary gbb-spacing">Update Notes</button>
                    </li>
                    <li class="uk-panel uk-panel-box uk-panel-box-secondary moderate-border">
                        <strong>Topic Creator:</strong>&nbsp;&nbsp;<span id="modal-moderate-user"></span>
                        
                        <br /><br />
                        
                        <?php if ($board->type == 1 || $board->type == 2) { ?>
                        <input type="checkbox" id="modal-moderate-approved" />&nbsp;this user is approved to <?php echo ($board->type == 2) ? 'post' : 'read and post'; ?> topics<br />
                        <?php } ?>
                        <?php if ($board->type == 0 || $board->type == 2) { ?>
                        <input type="checkbox" id="modal-moderate-banned" />&nbsp;ban this user from reading and posting topics<br />
                        <?php } ?>
                        
                        <br />
                        
                        <div class="uk-text-bold">Moderator Points</div>
                        <div class="uk-text-small gbb-spacing">
                            You can award up to 3 moderator points to this user, for this topic. These points will increase the user's reputation on your board, 
                            and it will allow others to see who provides quality content.
                        </div>
                        <div class="gbb-spacing">
                            <i id="modal-moderate-points-0" class="uk-icon uk-icon-times-circle-o" style="font-size: 200%"></i>
                            &nbsp;
                            <i id="modal-moderate-points-1" class="uk-icon uk-icon-certificate" style="font-size: 200%"></i>
                            &nbsp;
                            <i id="modal-moderate-points-2" class="uk-icon uk-icon-certificate" style="font-size: 200%"></i>
                            &nbsp;
                            <i id="modal-moderate-points-3" class="uk-icon uk-icon-certificate" style="font-size: 200%"></i>
                            &nbsp;
                            <span id="modal-moderate-points-text"></span>
                        </div>
                        
                        <br />
                        
                        <div id="modal-moderate-duplicate-none" class="uk-text-small gbb-spacing" style="display: none">
                            <div class="uk-alert uk-alert-info uk-margin-remove">
                                There are no other users posting from the same IP address.
                            </div>
                        </div>
                        
                        <div id="modal-moderate-duplicate-warn" class="uk-text-small gbb-spacing" style="display: none">
                            <div id="modal-moderate-duplicate-warn-toggle" class="uk-alert uk-alert-danger uk-margin-remove" data-uk-tooltip="{ pos: 'bottom' }" title="Click to View Details">
                                <i class="uk-icon uk-icon-exclamation-triangle"></i>&nbsp;&nbsp;Duplicate IP Address
                                <i id="modal-moderate-duplicate-warn-icon" class="uk-icon uk-icon-caret-square-o-up uk-align-right gbb-icon-large"></i>
                                
                                <div id="modal-moderate-duplicate-warn-more" style="display: none">
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
                                
                                <div id="modal-moderate-duplicate-topic-head" class="uk-grid">
                                    <div class="uk-width-2-10"><h5>User</h5></div>
                                    <div class="uk-width-8-10"><h5>Topic</h5></div>
                                </div>
                                
                                <div id="modal-moderate-duplicate-topic-rows" class="uk-grid gbb-spacing">
                                </div>
                                
                                <div id="modal-moderate-duplicate-reply-head" class="uk-grid gbb-spacing">
                                    <div class="uk-width-2-10"><h5>User</h5></div>
                                    <div class="uk-width-8-10"><h5>Reply</h5></div>
                                </div>
                                
                                <div id="modal-moderate-duplicate-reply-rows" class="uk-grid gbb-spacing">
                                </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                
                <br />
                
                <div class="uk-width-1-1 uk-text-right">
                    <a id="modal-moderate-reload" class="uk-button uk-button-primary">Close & Reload</a>
                    <a class="uk-button uk-button-primary uk-modal-close">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-report" class="uk-modal">
    <input type="hidden" id="modal-report-id" value="0" />
    
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title">
                    <span class="uk-text-bold uk-text-primary">Report Topic</span><br />
                    <span id="modal-report-title" class="uk-text-small"></span>
                </div>
                
                If you feel this topic goes against the rules / guidelines for <strong><?php echo $board->name; ?></strong> then please use this form to report it to the moderators.
                
                <br /><br />
                
                <div class="uk-text-small uk-align-right"><span id="editor-report-char">0</span> / 15,000 characters</div>
                
                <br />
                
                <textarea id="editor-report"></textarea>
                
                <div class="uk-alert uk-alert-info uk-text-small">
                <strong><i class="uk-icon uk-icon-exclamation-triangle"></i>&nbsp;&nbsp;Disclaimer</strong> - The communities on GrokBB vary greatly and certain language or behavior that is considered harmful on one board may be considered acceptable on another. 
                GrokBB doesn't moderate individual boards, nor do we know the dynamics of each community, and so only the moderator or board owner can provide reasons for their actions within that community.
                </div>
                
                <div class="uk-width-1-1 uk-text-right">
                    <span id="modal-report-msg" class="uk-alert uk-alert-danger" style="display: none"></span>
                    &nbsp;&nbsp;
                    <a class="uk-button uk-button-primary uk-modal-close">Close</a>
                    <a id="modal-report-send" class="uk-button uk-button-primary">Send Report</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-badges" class="uk-modal">
    <div class="uk-modal-dialog">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title uk-text-bold uk-text-primary"><?php echo $board->name; ?> Badges</div>
                <div class="uk-text-small"><span id="modal-badges-username" class="uk-text-bold"></span> has been awarded <span id="modal-badges-count"></span> for their knowledge, skills and / or contribution to this board.<br />You can hover over each badge to view the achievement they represent.</div>
                
                <div id="modal-badges-display" class="uk-grid gbb-spacing-large" data-uk-grid-margin></div>
                
                <br /><br />
                
                <div class="uk-width-1-1 uk-text-right">
                    <a class="uk-button uk-button-primary uk-modal-close">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gbb-header {
    background-image: url('<?php echo SITE_BASE_URL; ?>/img.php?bid=<?php echo $board->id; ?>');
    background-repeat: <?php echo ($board->header_back_repeat) ? 'repeat' : 'no-repeat'; ?>;
    /* background-color: <?php echo $board->header_back_color; ?>; */
}

.gbb-header-text {
    color: <?php echo $board->header_name_color; ?>;
    font-family: <?php echo $board->header_name_font; ?>;
}

.gbb-navbar, .header-subnav {
    background-color: <?php echo $board->header_menu_color; ?> !important;
}

.uk-button, .uk-nav-dropdown li, .sp-choose {
    font-family: <?php echo $board->button_font; ?> !important;
}

.uk-badge-success {
    background-color: <?php echo $board->button_color; ?> !important;
    color: <?php echo $board->button_text_color; ?> !important;
}

.uk-button-primary {
    background-color: <?php echo $board->button_color; ?> !important;
    color: <?php echo $board->button_text_color; ?> !important;
}

.uk-button-primary:hover {
    background-color: <?php echo $board->button_hover; ?> !important;
    color: <?php echo $board->button_text_hover; ?> !important;
}

.gbb-button-static:hover {
    background-color: <?php echo $board->button_color; ?> !important;
    text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.1); !important;
    color: <?php echo $board->button_text_color; ?> !important;
}

.sp-choose {
    background-color: <?php echo $board->button_color; ?> !important;
    color: <?php echo $board->button_text_color; ?> !important;
    border-color: <?php echo $board->button_color; ?> !important;
}

.tagItem {
    background-color: <?php echo $board->tag_color; ?> !important;
}

<?php echo str_replace('&amp;', '&', $board->stylesheet); ?>
</style>
