<?php
if (!isset($_SESSION['user'])) {
    header('Location: ' . SITE_BASE_URL);
}

require(SITE_BASE_APP . 'header.php');

$sort = (isset($_GET['sort'])) ? $_GET['sort'] : '';
if (!in_array($sort, array('dsaved', 'nreply', 'bdname', 'bcname', 'urname'))) { $sort = 'dsaved'; }

$orderBy = 'ut.saved DESC, t.created DESC';

switch ($sort) {
    case 'bdname' :
        $orderBy = 'b.name ASC, ' . $orderBy;
        break;
    case 'bcname' :
        $orderBy = 'bc.name ASC, ' . $orderBy;
        break;
    case 'urname' :
        $orderBy = 'u.username ASC, ' . $orderBy;
        break;
}

$topics = $GLOBALS['db']->getAll('topic t INNER JOIN ' . DB_PREFIX . 'user u ON t.id_ur = u.id INNER JOIN ' . DB_PREFIX . 'board b ON t.id_bd = b.id INNER JOIN ' . DB_PREFIX . 'board_category bc ON t.id_bc = bc.id LEFT JOIN ' . DB_PREFIX . 'user_topic ut ON t.id = ut.id_tc AND t.deleted = 0', 
    array('t.id_ur,ut.id_ur' => $_SESSION['user']->id), $orderBy, array('DISTINCT(t.id) as tid', 't.*', 'u.*', 'u.id as uid', 'IF (ut.id, ut.saved, t.created) as created', 'b.name as bdname', 'bc.id as bcid', 'bc.name as bcname', 'bc.color as bccolor', 'bc.image as bcimage'));

$topicIDs = array();

if ($topics) {
    foreach ($topics as $topic) {
        $topicIDs[] = $topic->tid;
    }
}

if ($topicIDs) {
    $newReplies = GrokBB\Topic::newReplies($topicIDs);
    
    $topicMedia = $GLOBALS['db']->getAll('topic_media', array('id_tc' => array('IN', $topicIDs)), 'id');
    $topicMediaFirst = array();
    
    if ($topicMedia) {
        foreach ($topicMedia as $tm) {
            if (!in_array($tm->id_tc, array_keys($topicMediaFirst))) {
                $topicMediaFirst[$tm->id_tc] = $tm;
            }
        }
    }
    
    if ($sort == 'nreply') {
        usort($topics, function($a, $b) {
            global $newReplies;
            
            $aTmp = $newReplies[$a->tid];
            $c = ($aTmp == -1) ? $a->counter_replies : (int) $aTmp;
            
            $bTmp = $newReplies[$b->tid];
            $d = ($bTmp == -1) ? $b->counter_replies : (int) $bTmp;
            
            if ($c == $d) {
                if ($a->created == $b->created) {
                    return 0;
                } else {
                    return ($a->created > $b->created) ? -1 : 1;
                }
            }
            
            return ($c > $d) ? -1 : 1;
        });
    }
}
?>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-badge uk-badge uk-badge-success"><?php echo count($topics); ?></div>
    <h3 class="uk-panel-title uk-text-primary uk-text-bold">My Topics</h3>
    
    <div class="uk-grid" data-uk-grid-margin>
        <?php if ($topics) { ?>
        <div class="uk-width-small-9-10 uk-width-medium-8-10 uk-width-large-6-10 uk-width-xlarge-4-10 uk-align-center uk-margin-bottom-remove gbb-spacing">
            <span class="uk-text-small uk-text-bold uk-hidden-xsmall">Sort Topics By</span>&nbsp;
            <a href="<?php echo SITE_BASE_URL . '/user/topics/?sort=dsaved'; ?>" class="uk-button uk-button-small gbb-button-static <?php echo ($sort == 'dsaved') ? 'uk-button-primary' : ''; ?>"><span class="uk-hidden-xsmall">Date </span>Saved</a>&nbsp;
            <a href="<?php echo SITE_BASE_URL . '/user/topics/?sort=nreply'; ?>" class="uk-button uk-button-small gbb-button-static <?php echo ($sort == 'nreply') ? 'uk-button-primary' : ''; ?>"><span class="uk-hidden-xsmall">New </span>Replies</a>&nbsp;
            <a href="<?php echo SITE_BASE_URL . '/user/topics/?sort=bdname'; ?>" class="uk-button uk-button-small gbb-button-static <?php echo ($sort == 'bdname') ? 'uk-button-primary' : ''; ?>">Board<span class="uk-hidden-xsmall"> Name</span></a>&nbsp;
            <!--<span class="uk-visible-xsmall" style="padding: 65px"><br/><br />&nbsp;</span>-->
            <a href="<?php echo SITE_BASE_URL . '/user/topics/?sort=bcname'; ?>" class="uk-button uk-button-small gbb-button-static <?php echo ($sort == 'bcname') ? 'uk-button-primary' : ''; ?>">Cat<span class="uk-hidden-xsmall">egory</span></a>&nbsp;
            <a href="<?php echo SITE_BASE_URL . '/user/topics/?sort=urname'; ?>" class="uk-button uk-button-small gbb-button-static <?php echo ($sort == 'urname') ? 'uk-button-primary' : ''; ?>">User<span class="uk-hidden-xsmall">name</span></a>
        </div>
        <?php foreach ($topics as $topic) { ?>
        <div id="topic-<?php echo $topic->tid; ?>" class="uk-width-xsmall-1-1 uk-width-small-3-4 uk-align-center uk-margin-bottom-remove">
            <div class="uk-panel uk-panel-box uk-padding-remove">
                <div class="uk-grid uk-grid-collapse topic-padding">
                    <div class="uk-panel-badge topic-padding">
                        <div class="uk-text-small uk-float-right uk-text-bold topic-saved">
                            <?php if ($_SESSION['user']->id != $topic->id_ur) { ?>
                            <i id="topic-unsave-<?php echo $topic->tid; ?>" class="uk-icon uk-icon-remove uk-align-right" data-uk-tooltip="{ pos: 'top-right' }" title="Unsave Topic"></i>
                            <?php } ?>
                            <span class="uk-hidden-xsmall">Saved <?php echo GrokBB\Util::getTimespan($topic->created, 1); ?> ago</span>
                            <div class="topic-info uk-hidden-xsmall"><a href="<?php echo SITE_BASE_URL . '/g/' . str_replace(' ', '_', $topic->bdname); ?>"><?php echo $topic->bdname; ?></a></div>
                        </div>
                    </div>
                    
                    <div id="topic-user-<?php echo $topic->id_ur; ?>" class="uk-width-xsmall-2-10 uk-width-small-2-10 uk-width-medium-1-10 uk-text-center gbb-padding topic-user" data-uk-tooltip="{ pos: 'bottom-left' }" title="Joined: <?php echo ltrim(GrokBB\Util::getTimespan($topic->joined, 1), 0); ?> ago<br>Click to View Profile">
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
                            <div class="uk-text-small uk-text-bold gbb-spacing-small" style="position: relative; top: 3px"><?php echo $topic->username; ?></div>
                        </div>
                    </div>
                    
                    <div class="uk-width-xsmall-7-10 uk-width-small-5-10 uk-width-medium-6-10 gbb-padding">
                        <h3 class="uk-text-bold uk-margin-remove"><span data-uk-tooltip="{ pos: 'right' }" title="Click to View Topic"><a href="<?php echo SITE_BASE_URL . '/g/' . str_replace(' ', '_', $topic->bdname) . '/view/' . $topic->tid; ?>" class="topic-link"><?php echo $topic->title; ?></a>&nbsp;</span></h3>
                        
                        <div class="uk-width-1-1 uk-text-small uk-text-bold gbb-spacing-small">
                            Views:&nbsp;<div class="uk-badge uk-badge-success"><?php echo $topic->counter_views; ?></div>&nbsp;&nbsp;&nbsp;
                            Replies:&nbsp;<div class="uk-badge uk-badge-success"><?php echo $topic->counter_replies; ?></div>
                            <i class="uk-icon-level-up">&nbsp;</i><div class="uk-badge uk-badge-danger" data-uk-tooltip="{ pos: 'top-left' }" title="New Replies"><?php echo ($newReplies[$topic->tid] != -1) ? $newReplies[$topic->tid] : $topic->counter_replies; ?></div>
                        </div>
                        
                        <div class="uk-width-1-1 gbb-spacing-large">
                            <?php if ($topic->bcimage) { ?>
                            <img src="<?php echo SITE_BASE_URL . '/img.php?bid=' . $board->id . '&cat=' . $topic->bcid; ?>" width="200" height="40" border="0">
                            <?php } else { ?>
                            <figure class="uk-overlay" style="background-color: <?php echo $topic->bccolor; ?>">
                                <img src="<?php echo SITE_BASE_URL . '/img/category.png'; ?>" width="200" height="40" border="0" title="<?php echo $topic->bcname; ?>" alt="<?php echo $topic->bcname; ?>">
                                <figcaption class="uk-overlay-panel uk-flex uk-flex-middle"><?php echo $topic->bcname; ?></figcaption>
                            </figure>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php } else { ?>
        <div class="uk-width-1-1">
            <div class="uk-alert uk-alert-info" data-uk-alert>You have no saved topics.</div>
        </div>
        <?php } ?>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>