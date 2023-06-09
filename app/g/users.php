<?php
$GLOBALS['includeCharts'] = true;
$GLOBALS['includeEditor'] = true;

require(SITE_BASE_APP . 'header.php');

$months[] = strtoupper(date('M y'));
                        
for ($i = 1; $i <= 18; $i++) {
    $months[] = strtoupper(date('M y', strtotime('-' . $i . ' month', strtotime('first day of this month'))));
}

$months = array_reverse($months);

// subscribers

$subDataRawDB = $GLOBALS['db']->custom('SELECT FROM_UNIXTIME(ub.added, "%y") AS year, FROM_UNIXTIME(ub.added, "%M") AS month, COUNT(DISTINCT(ub.id_ur)) AS count FROM ' . DB_PREFIX . 'user_board ub ' .
                                       'WHERE ub.id_bd = ' . $board->id . ' AND DATE_SUB(now(), INTERVAL 18 MONTH) GROUP BY FROM_UNIXTIME(ub.added, "%Y%m")');
$subDataChart = array();

foreach ($months as $month) {
    $count = 0;
    
    foreach ($subDataRawDB as $raw) {
        if ($month == strtoupper(substr($raw->month, 0, 3)) . ' ' . $raw->year) {
            $count = $raw->count; break;
        }
    }
    
    $subDataChart[] = $count;
}

// visitors

$visDataRawDB = $GLOBALS['db']->custom('SELECT FROM_UNIXTIME(tv.viewed, "%y") AS year, FROM_UNIXTIME(tv.viewed, "%M") AS month, COUNT(*) AS count FROM ' . DB_PREFIX . 'topic_view tv ' .
                                       'INNER JOIN ' . DB_PREFIX . 'topic t ON tv.id_tc = t.id WHERE t.id_bd = ' . $board->id . ' AND DATE_SUB(now(), INTERVAL 18 MONTH) GROUP BY FROM_UNIXTIME(tv.viewed, "%Y%m")');
$visDataChart = array();

foreach ($months as $month) {
    $count = 0;
    
    foreach ($visDataRawDB as $raw) {
        if ($month == strtoupper(substr($raw->month, 0, 3)) . ' ' . $raw->year) {
            $count = $raw->count; break;
        }
    }
    
    $visDataChart[] = $count;
}

// new topics

$topDataRawDB = $GLOBALS['db']->custom('SELECT FROM_UNIXTIME(t.created, "%y") AS year, FROM_UNIXTIME(t.created, "%M") AS month, COUNT(*) AS count FROM ' . DB_PREFIX . 'topic t ' .
                                       'WHERE t.id_bd = ' . $board->id . ' AND DATE_SUB(now(), INTERVAL 18 MONTH) GROUP BY FROM_UNIXTIME(t.created, "%Y%m")');
$topDataChart = array();

foreach ($months as $month) {
    $count = 0;
    
    foreach ($topDataRawDB as $raw) {
        if ($month == strtoupper(substr($raw->month, 0, 3)) . ' ' . $raw->year) {
            $count = $raw->count; break;
        }
    }
    
    $topDataChart[] = $count;
}

// new replies

$repDataRawDB = $GLOBALS['db']->custom('SELECT FROM_UNIXTIME(r.created, "%y") AS year, FROM_UNIXTIME(r.created, "%M") AS month, COUNT(*) AS count FROM ' . DB_PREFIX . 'reply r ' .
                                       'INNER JOIN ' . DB_PREFIX . 'topic t ON r.id_tc = t.id WHERE t.id_bd = ' . $board->id . ' AND DATE_SUB(now(), INTERVAL 18 MONTH) GROUP BY FROM_UNIXTIME(r.created, "%Y%m")');
$repDataChart = array();

foreach ($months as $month) {
    $count = 0;
    
    foreach ($repDataRawDB as $raw) {
        if ($month == strtoupper(substr($raw->month, 0, 3)) . ' ' . $raw->year) {
            $count = $raw->count; break;
        }
    }
    
    $repDataChart[] = $count;
}

// topic categories

$catDataRawDB = $GLOBALS['db']->custom('SELECT bc.name, COUNT(*) AS count FROM ' . DB_PREFIX . 'topic t INNER JOIN ' . DB_PREFIX . 'board_category bc ON t.id_bc = bc.id ' .
                                       'WHERE t.id_bd = ' . $board->id . ' GROUP BY bc.name');

if ($catDataRawDB) {
    $catDataNames = array();
    $catDataChart = array();
    $catDataColor = array();
    $catDataCount = count($catDataRawDB);
    $catDataTotal = 0;
    
    foreach ($catDataRawDB as $key => $raw) {
        $catDataTotal += $raw->count;
    }
    
    foreach ($catDataRawDB as $key => $raw) {
        $catDataNames[] = $raw->name;
        $catDataChart[] = round(($raw->count / $catDataTotal) * 100);
        $catDataColor[] = 'hsl(' . ((($key + 1) / $catDataCount) * 360) . ', 75%, 65%)';
    }
} else {
    $catDataNames = array('No Topics');
    $catDataChart = array(100);
    $catDataColor = array('hsl(360, 75%, 65%)');
}

// topic tags

$tagDataRawDB = $GLOBALS['db']->custom('SELECT tt.name, COUNT(*) AS count FROM ' . DB_PREFIX . 'topic_tag tt INNER JOIN ' . DB_PREFIX . 'topic t ON tt.id_tc = t.id ' .
                                       'WHERE t.id_bd = ' . $board->id . ' GROUP BY tt.name HAVING COUNT(*) > 2 UNION ' .
                                       'SELECT "other" AS name, SUM(o.count) AS count FROM (' . 
                                           'SELECT COUNT(*) AS count FROM ' . DB_PREFIX . 'topic_tag tt INNER JOIN ' . DB_PREFIX . 'topic t ON tt.id_tc = t.id ' .
                                           'WHERE t.id_bd = ' . $board->id . ' GROUP BY tt.name HAVING COUNT(*) <= 2' .
                                       ') AS o HAVING SUM(o.count) > 0');

if ($tagDataRawDB) {
    $tagDataNames = array();
    $tagDataChart = array();
    $tagDataColor = array();
    $tagDataCount = count($tagDataRawDB);
    $tagDataTotal = 0;
    
    // foreach ($tagDataRawDB as $key => $raw) {
    //     $tagDataTotal += $raw->count;
    // }
    
    foreach ($tagDataRawDB as $key => $raw) {
        $tagDataNames[] = $raw->name;
        $tagDataChart[] = $raw->count;
        // $tagDataChart[] = round(($raw->count / $tagDataTotal) * 100);
        $tagDataColor[] = 'hsl(' . ((($key + 1) / $tagDataCount) * 360) . ', 75%, 65%)';
    }
} else {
    $tagDataNames = array('No Tags');
    $tagDataChart = array(1);
    // $tagDataChart = array(100);
    $tagDataColor = array('hsl(360, 75%, 65%)');
}

// topics with most views

$topicViews = $GLOBALS['db']->custom('SELECT t.id, t.title, COUNT(*) AS count FROM ' . DB_PREFIX . 'topic t INNER JOIN ' . DB_PREFIX . 'topic_view tv ON t.id = tv.id_tc ' .
                                     'WHERE t.id_bd = ' . $board->id . ' GROUP BY t.id ORDER BY COUNT(*) DESC LIMIT 5');

// topics with most replies

$topicReply = $GLOBALS['db']->custom('SELECT t.id, t.title, COUNT(*) AS count FROM ' . DB_PREFIX . 'topic t INNER JOIN ' . DB_PREFIX . 'reply r ON t.id = r.id_tc ' .
                                     'WHERE t.id_bd = ' . $board->id . ' GROUP BY t.id ORDER BY COUNT(*) DESC LIMIT 5');

// users with most topics

$usersTopic = $GLOBALS['db']->custom('SELECT u.id, u.username, COUNT(t.id) AS count FROM ' . DB_PREFIX . 'user u INNER JOIN ' . DB_PREFIX . 'topic t ON u.id = t.id_ur ' .
                                     'INNER JOIN ' . DB_PREFIX . 'user_board ub ON u.id = ub.id_ur WHERE ub.id_bd = ' . $board->id . ' AND ub.deleted = 0 ' .
                                     'GROUP BY u.id ORDER BY COUNT(t.id) DESC LIMIT 5');

// users with most replies

$usersReply = $GLOBALS['db']->custom('SELECT u.id, u.username, COUNT(r.id) AS count FROM ' . DB_PREFIX . 'user u INNER JOIN ' . DB_PREFIX . 'reply r ON u.id = r.id_ur ' .
                                     'INNER JOIN ' . DB_PREFIX . 'user_board ub ON u.id = ub.id_ur WHERE ub.id_bd = ' . $board->id . ' AND ub.deleted = 0 ' .
                                     'GROUP BY u.id ORDER BY COUNT(r.id) DESC LIMIT 5');

// users with most reputation

$rep = 'ROUND( COALESCE((((ubs.counter_topics / b.counter_topics) * 100) * 0.3), 0) + COALESCE((((ubs.counter_replies / b.counter_replies) * 100) * 0.2), 0) + COALESCE((((ubs.counter_points / b.counter_points) * 100) * 0.5), 0) )';
$usersRep = $GLOBALS['db']->custom('SELECT u.id, u.username, ' . $rep . ' AS reputation FROM ' . DB_PREFIX . 'user u INNER JOIN ' . DB_PREFIX . 'user_board ub ON u.id = ub.id_ur ' .
                                   'INNER JOIN ' . DB_PREFIX . 'board b ON ub.id_bd = b.id INNER JOIN ' . DB_PREFIX . 'user_board_stats ubs ON b.id = ubs.id_bd AND u.id = ubs.id_ur ' .
                                   'WHERE ub.id_bd = ' . $board->id . ' AND ub.deleted = 0 GROUP BY u.id ORDER BY ' . $rep . ' DESC LIMIT 5');

// newest users

$usersNew = $GLOBALS['db']->custom('SELECT u.id, u.username, ub.added FROM ' . DB_PREFIX . 'user u INNER JOIN ' . DB_PREFIX . 'user_board ub ON u.id = ub.id_ur ' .
                                   'WHERE ub.id_bd = ' . $board->id . ' AND ub.deleted = 0 ORDER BY ub.added DESC LIMIT 5');

// announcements

$announcements = $GLOBALS['db']->getAll('board_announcement', array('id_bd' => $board->id, 'deleted' => 0), 'updated DESC');

// approved users

if ($board->type == 1 || $board->type == 2) {
    $approvedUsers = $GLOBALS['db']->custom('SELECT u.*, uba.approved FROM ' . DB_PREFIX . 'user u INNER JOIN ' . DB_PREFIX . 'user_board_approved uba ON u.id = uba.id_ur WHERE uba.id_bd = ' . $board->id . ' ORDER BY uba.approved DESC');
    $approvedUsersCount = ($approvedUsers) ? count($approvedUsers) : 0;
}

// moderators

$moderateUsers = $GLOBALS['db']->custom('SELECT u.*, ubm.added FROM ' . DB_PREFIX . 'user u INNER JOIN ' . DB_PREFIX . 'user_board_moderator ubm ON u.id = ubm.id_ur WHERE ubm.id_bd = ' . $board->id . ' ORDER BY ubm.added DESC');
$moderateUsersCount = ($moderateUsers) ? count($moderateUsers) : 0;

// banned users

$bannedUsers = $GLOBALS['db']->custom('SELECT u.*, ubb.banned FROM ' . DB_PREFIX . 'user u INNER JOIN ' . DB_PREFIX . 'user_board_banned ubb ON u.id = ubb.id_ur WHERE ubb.id_bd = ' . $board->id . ' ORDER BY ubb.banned DESC');
$bannedUsersCount = ($bannedUsers) ? count($bannedUsers) : 0;

// badges

$badges = $GLOBALS['db']->getAll('board_badge bb LEFT JOIN ' . DB_PREFIX . 'user_board_badge ubb ON bb.id = ubb.id_bb', array('bb.id_bd' => $board->id), '`desc`', array('bb.*', 'COUNT(ubb.id) as counter_users'), false, false, 'bb.id');
?>

<div class="uk-grid uk-grid-small" data-uk-grid-margin>
    <input type="hidden" id="board-id" value="<?php echo $board->id; ?>">
    
    <div class="uk-width-large-3-4">
        <ul class="uk-tab uk-tab-grid" data-uk-tab="{ connect: '#users-tabs' }">
            <?php if ($_SESSION['user']->isOwner == $board->id) { ?>
            <li class="uk-width-xsmall-1-4 uk-width-small-1-6" id="overview"><a href="#">Overview</a></li>
            <li class="uk-width-xsmall-1-4 uk-width-small-2-6 uk-width-xlarge-1-6" id="users"><a href="#">Users<span class="uk-hidden-xsmall"> & Moderators</span></a></li>
            <li class="uk-width-xsmall-1-4 uk-width-small-2-6 uk-width-xlarge-1-6" id="announcements"><a href="#" class="uk-text-truncate">Announcements</a></li>
            <?php } else { ?>
            <li class="uk-width-xsmall-1-4 uk-width-small-1-6" id="users"><a href="#">Moderate Users</a></li>
            <?php } ?>
            <li class="uk-width-xsmall-1-4 uk-width-small-1-6" id="badges"><a href="#"><span class="uk-hidden-xsmall uk-hidden-small">User </span>Badges</a></li>
        </ul>
        
        <ul id="users-tabs" class="uk-switcher">
            <?php if ($_SESSION['user']->isOwner == $board->id) { ?>
            <li class="uk-panel uk-panel-box">
                <h4 class="uk-text-primary" style="display: inline">☆*。<strong><?php echo $board->name; ?></strong></h4>
                &nbsp;&nbsp;<span class="uk-text-small" style="position: relative; bottom: 1px">emerged from the void <strong><span class="uk-hidden-xsmall"><?php echo GrokBB\Util::getTimespan($board->created, 3); ?></span><span class="uk-visible-xsmall"><?php echo GrokBB\Util::getTimespan($board->created, 1); ?></span></strong> ago</span>
                
                <br /><br />
                
                <div class="uk-grid uk-text-small">
                    <div class="uk-width-1-1">
                        <strong>Total Subscribers:</strong>
                        &nbsp;<?php echo $board->counter_users; ?>
                        
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        
                        <strong>Total Topics:</strong>
                        &nbsp;<?php echo $board->counter_topics; ?>
                        
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        
                        <strong>Total Replies:</strong>
                        &nbsp;<?php echo $board->counter_replies; ?>
                    </div>
                </div>
                
                <hr class="uk-grid-divider uk-margin-remove gbb-spacing gbb-padding">
                
                <div class="uk-grid">
                    <div class="uk-width-xlarge-1-2">
                        <canvas id="chartSubscribers" height="100"></canvas>

                        <script>
                        $(document).ready(function() {
                            setTimeout(function() {
                                var myLineOptions = {
                                    label: 'Subscribers',
                                     data: [<?php echo implode(',', $subDataChart); ?>]
                                }
                                
                                $.extend(myLineOptions, chartJsLineOptions);
                                
                                new Chart($('#chartSubscribers'), {
                                    type: 'line',
                                    data: {
                                        labels: [<?php echo '"' . implode('","', $months) . '"'; ?>],
                                        datasets: [ myLineOptions ]
                                    }
                                });
                            }, 0);
                        });
                        </script>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <canvas id="chartVisitors" height="100"></canvas>

                        <script>
                        $(document).ready(function() {
                            setTimeout(function() {
                                var myLineOptions = {
                                    label: 'Visitors',
                                     data: [<?php echo implode(',', $visDataChart); ?>]
                                }
                                
                                $.extend(myLineOptions, chartJsLineOptions);
                                
                                new Chart($('#chartVisitors'), {
                                    type: 'line',
                                    data: {
                                        labels: [<?php echo '"' . implode('","', $months) . '"'; ?>],
                                        datasets: [ myLineOptions ]
                                    }
                                });
                            }, 0);
                        });
                        </script>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <canvas id="chartTopics" height="100"></canvas>

                        <script>
                        $(document).ready(function() {
                            setTimeout(function() {
                                var myLineOptions = {
                                    label: 'New Topics',
                                     data: [<?php echo implode(',', $topDataChart); ?>]
                                }
                                
                                $.extend(myLineOptions, chartJsLineOptions);
                                
                                new Chart($('#chartTopics'), {
                                    type: 'line',
                                    data: {
                                        labels: [<?php echo '"' . implode('","', $months) . '"'; ?>],
                                        datasets: [ myLineOptions ]
                                    }
                                });
                            }, 0);
                        });
                        </script>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <canvas id="chartReplies" height="100"></canvas>

                        <script>
                        $(document).ready(function() {
                            setTimeout(function() {
                                var myLineOptions = {
                                    label: 'New Replies',
                                     data: [<?php echo implode(',', $repDataChart); ?>]
                                }
                                
                                $.extend(myLineOptions, chartJsLineOptions);
                                
                                new Chart($('#chartReplies'), {
                                    type: 'line',
                                    data: {
                                        labels: [<?php echo '"' . implode('","', $months) . '"'; ?>],
                                        datasets: [ myLineOptions ]
                                    }
                                });
                            }, 0);
                        });
                        </script>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <h4>Topic Categories</h4>
                        <canvas id="chartTopicCategories" height="100"></canvas>

                        <script>
                        $(document).ready(function() {
                            setTimeout(function() {
                                var data = {
                                      labels: [<?php echo '"' . implode('","', str_replace('"', '\"', $catDataNames)) . '"'; ?>],
                                    datasets: [{
                                            data: [<?php echo implode(',', $catDataChart); ?>],
                                            backgroundColor: [<?php echo '"' . implode('","', str_replace('"', '\"', $catDataColor)) . '"'; ?>]
                                    }]
                                };
                                
                                new Chart($('#chartTopicCategories'), {
                                       type: 'pie',
                                       data: data,
                                    options: {
                                        tooltips: {
                                            callbacks: {
                                                label: function(item, data) {
                                                    return data.labels[item.index] + ': ' + data.datasets[0].data[item.index] + '%';
                                                }
                                            }
                                        }
                                    }
                                });
                            }, 0);
                        });
                        </script>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <h4>Topic Tags</h4>
                        <canvas id="chartTopicTags" height="100"></canvas>

                        <script>
                        $(document).ready(function() {
                            setTimeout(function() {
                                var data = {
                                      labels: [<?php echo '"' . implode('","', str_replace('"', '\"', $tagDataNames)) . '"'; ?>],
                                    datasets: [{
                                            data: [<?php echo implode(',', $tagDataChart); ?>],
                                            backgroundColor: [<?php echo '"' . implode('","', str_replace('"', '\"', $tagDataColor)) . '"'; ?>]
                                    }]
                                };
                                
                                new Chart($('#chartTopicTags'), {
                                       type: 'pie',
                                       data: data,
                                    options: {
                                        tooltips: {
                                            callbacks: {
                                                label: function(item, data) {
                                                    var topicLabel = data.labels[item.index];
                                                    var topicCount = data.datasets[0].data[item.index];
                                                    
                                                    if (topicLabel == 'No Tags') {
                                                        return topicLabel;
                                                    } else {
                                                        return topicLabel + ': ' + topicCount + ' Topics';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            }, 0);
                        });
                        </script>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <br />
                        
                        <h4>Topics with Most Views</h4>
                        
                        <?php if ($topicViews) { ?>
                        <table class="uk-table">
                        <tr>
                            <th class="uk-width-1-10 uk-text-right">Views</th>
                            <th class="uk-width-7-10">Topic Title</th>
                        </tr>
                        <?php foreach ($topicViews as $topic) { ?>
                        <tr>
                            <td class="uk-text-right"><?php echo $topic->count; ?></td>
                            <td><a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/' . $topic->id; ?>"><?php echo $topic->title; ?></a></td>
                        </tr>
                        <?php } ?>
                        </table>
                        <?php } else { ?>
                        <div class="uk-alert uk-alert-info" data-uk-alert>
                            There are no topics that have been viewed.
                        </div>
                        <?php } ?>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <br />
                        
                        <h4>Topics with Most Replies</h4>
                        
                        <?php if ($topicReply) { ?>
                        <table class="uk-table">
                        <tr>
                            <th class="uk-width-1-10 uk-text-right">Replies</th>
                            <th class="uk-width-7-10">Topic Title</th>
                        </tr>
                        <?php foreach ($topicReply as $topic) { ?>
                        <tr>
                            <td class="uk-text-right"><?php echo $topic->count; ?></td>
                            <td><a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/' . $topic->id; ?>"><?php echo $topic->title; ?></a></td>
                        </tr>
                        <?php } ?>
                        </table>
                        <?php } else { ?>
                        <div class="uk-alert uk-alert-info" data-uk-alert>
                            There are no topics that have replies.
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </li>
            <?php } ?>
            
            <li class="uk-panel uk-panel-box">
                <div class="uk-grid gbb-spacing" data-uk-grid-margin>
                    <div class="uk-width-xlarge-1-2">
                        <h4>Users with Most Topics</h4>
                        
                        <?php if ($usersTopic) { ?>
                        <table class="uk-table">
                        <tr>
                            <th class="uk-text-right">Topics</th>
                            <th class="uk-width-xsmall-7-10 uk-width-small-8-10">Username</th>
                        </tr>
                        <?php foreach ($usersTopic as $user) { ?>
                        <tr>
                            <td class="uk-text-right"><?php echo $user->count; ?></td>
                            <td class="uk-width-xsmall-7-10 uk-width-small-8-10"><a href="<?php echo SITE_BASE_URL . '/user/view/' . $user->id; ?>"><?php echo $user->username; ?></a></td>
                        </tr>
                        <?php } ?>
                        </table>
                        <?php } else { ?>
                        <div class="uk-alert uk-alert-info" data-uk-alert>
                            There are no users who have posted topics.
                        </div>
                        <?php } ?>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <h4>Users with Most Replies</h4>
                        
                        <?php if ($usersReply) { ?>
                        <table class="uk-table">
                        <tr>
                            <th class="uk-text-right">Replies</th>
                            <th class="uk-width-xsmall-7-10 uk-width-small-8-10">Username</th>
                        </tr>
                        <?php foreach ($usersReply as $user) { ?>
                        <tr>
                            <td class="uk-text-right"><?php echo $user->count; ?></td>
                            <td class="uk-width-xsmall-7-10 uk-width-small-8-10"><a href="<?php echo SITE_BASE_URL . '/user/view/' . $user->id; ?>"><?php echo $user->username; ?></a></td>
                        </tr>
                        <?php } ?>
                        </table>
                        <?php } else { ?>
                        <div class="uk-alert uk-alert-info" data-uk-alert>
                            There are no users who have posted replies.
                        </div>
                        <?php } ?>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <h4>Users with Most Reputation</h4>
                        
                        <?php if ($usersRep) { ?>
                        <table class="uk-table">
                        <tr>
                            <th class="uk-text-right">Reputation</th>
                            <th class="uk-width-xsmall-7-10 uk-width-small-8-10">Username</th>
                        </tr>
                        <?php foreach ($usersRep as $user) { ?>
                        <tr>
                            <td class="uk-text-right"><?php echo $user->reputation; ?></td>
                            <td class="uk-width-xsmall-7-10 uk-width-small-8-10"><a href="<?php echo SITE_BASE_URL . '/user/view/' . $user->id; ?>"><?php echo $user->username; ?></a></td>
                        </tr>
                        <?php } ?>
                        </table>
                        <?php } else { ?>
                        <div class="uk-alert uk-alert-info" data-uk-alert>
                            There are no subscribed users.
                        </div>
                        <?php } ?>
                    </div>
                    <div class="uk-width-xlarge-1-2">
                        <h4>Newest Users</h4>
                        
                        <?php if ($usersNew) { ?>
                        <table class="uk-table">
                        <tr>
                            <th class="uk-text-right">Subscribed</th>
                            <th class="uk-width-xsmall-7-10 uk-width-small-8-10">Username</th>
                        </tr>
                        <?php foreach ($usersNew as $user) { ?>
                        <tr>
                            <td class="uk-text-right"><?php echo str_replace('second', 'sec', str_replace('minute', 'min', \GrokBB\Util::getTimespan($user->added, 1))); ?> ago</td>
                            <td class="uk-width-xsmall-7-10 uk-width-small-8-10"><a href="<?php echo SITE_BASE_URL . '/user/view/' . $user->id; ?>"><?php echo $user->username; ?></a></td>
                        </tr>
                        <?php } ?>
                        </table>
                        <?php } else { ?>
                        <div class="uk-alert uk-alert-info" data-uk-alert>
                            There are no subscribed users.
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php if ($board->type == 1 || $board->type == 2) { ?>
                <div class="uk-form uk-grid">
                    <div class="uk-width-1-1">
                        <fieldset>
                            <legend>Approved Users</legend>
                            
                            <input type="text" id="approved-username" class="uk-width-xsmall-6-10 uk-width-small-3-10" placeholder="Search for a username ...">
                            &nbsp;&nbsp;<button id="approved-user-add" class="uk-button uk-button-primary users-button">Approve User</button>
                            
                            &nbsp;&nbsp;
                            
                            <a class="uk-text-small" data-uk-modal="{ target: '#modal-approved-users', bgclose: false, center: true }">You have <span id="approved-user-count"><?php echo $approvedUsersCount; ?></span> approved users</a>
                            
                            <span class="uk-hidden-xsmall">&nbsp;&nbsp;</span>
                            
                            <span id="approved-user-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                        </fieldset>
                    </div>
                </div>
                <?php } ?>
                <?php if ($_SESSION['user']->isOwner == $board->id) { ?>
                <div class="uk-form uk-grid">
                    <div class="uk-width-1-1">
                        <fieldset>
                            <legend>Board Moderators</legend>
                            
                            <input type="text" id="moderate-username" class="uk-width-xsmall-6-10 uk-width-small-3-10" placeholder="Search for a username ...">
                            &nbsp;&nbsp;<button id="moderate-user-add" class="uk-button uk-button-primary users-button">Add User</button>
                            
                            <span class="uk-hidden-xsmall">&nbsp;&nbsp;</span>
                            
                            <a class="uk-text-small" data-uk-modal="{ target: '#modal-moderate-users', bgclose: false, center: true }">You have <span id="moderate-user-count"><?php echo $moderateUsersCount; ?></span> moderators</a>
                            
                            &nbsp;&nbsp;
                            
                            <span id="moderate-user-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                        </fieldset>
                    </div>
                </div>
                <?php } ?>
                <?php if ($board->type == 0 || $board->type == 2) { ?>
                <div class="uk-form uk-grid">
                    <div class="uk-width-1-1">
                        <fieldset>
                            <legend>Banned Users</legend>
                            
                            <input type="text" id="banned-username" class="uk-width-xsmall-6-10 uk-width-small-3-10" placeholder="Search for a username ...">
                            &nbsp;&nbsp;<button id="banned-user-add" class="uk-button uk-button-primary users-button">Add User</button>
                            
                            <span class="uk-hidden-xsmall">&nbsp;&nbsp;</span>
                            
                            <a class="uk-text-small" data-uk-modal="{ target: '#modal-banned-users', bgclose: false, center: true }">You have <span id="banned-user-count"><?php echo $bannedUsersCount; ?></span> banned users</a>
                            
                            &nbsp;&nbsp;
                            
                            <span id="banned-user-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                        </fieldset>
                    </div>
                </div>
                <?php } ?>
            </li>
            
            <?php if ($_SESSION['user']->isOwner == $board->id) { ?>
            <li class="uk-panel uk-panel-box">
                <a name="announcements-create"></a>
                
                <input type="hidden" id="announcement-id" value="0">
                
                <div class="uk-panel-title uk-clearfix uk-margin-remove">
                    <span class="uk-text-bold uk-text-primary">Announcements</span>
                    <button id="announcement-save" class="uk-button uk-button-primary uk-align-right gbb-editor-button">Save Announcement</button>
                    <button id="announcement-save-cancel" class="uk-button uk-button-primary uk-align-right gbb-editor-button" style="display: none">Cancel Edit</button>
                    <div class="uk-text-small uk-align-right gbb-editor-characters uk-hidden-xsmall"><span id="announcement-editor-char">0</span> / 15,000 characters</div>
                </div>
                
                <div class="gbb-line-height">
                    This can be used to send board announcements to all your subscribed users.<span class="uk-hidden-xsmall uk-hidden-small"> Saving an announcement will only create a draft in the <strong>Saved Announcements</strong> below.
                    You will need to click the <button class="uk-button uk-button-primary">Send</button> button for it to appear in your user's messages. You can <button class="uk-button uk-button-danger">Recall</button> it at any time too.</span>
                </div>
                
                <br />
                
                <div id="announcement-msg-div" style="display: none">
                    <span id="announcement-msg" class="uk-alert uk-alert-danger uk-text-small"></span>
                </div>
                
                <div class="uk-form uk-form-stacked">
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="announcement-subject">Subject</label>
                        <div class="uk-form-controls">
                            <input class="uk-width-100" type="text" placeholder="" id="announcement-subject" maxlength="60">
                        </div>
                    </div>
                
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="announcement-editor">Announcement</label>
                        <div class="uk-form-controls">
                            <textarea id="announcement-editor"></textarea>
                        </div>
                    </div>
                </div>
                
                <br />
                
                <a name="announcements-drafts"></a>
                
                <?php if ($announcements) { ?>
                <h4><strong>Saved Announcements</strong></h4>
                
                <table class="uk-table uk-table-striped">
                <tr>
                    <th class="uk-width-4-10">Subject</th>
                    <th class="uk-width-4-10">Sent</th>
                    <th class="uk-width-2-10 uk-text-center">Actions</th>
                </tr>
                <?php foreach ($announcements as $announcement) { ?>
                <tr id="announcement-row-<?php echo $announcement->id; ?>">
                    <td class="gbb-editor-characters" id="announcement-subject-<?php echo $announcement->id; ?>"><?php echo $announcement->subject; ?></td>
                    <td class="gbb-editor-characters"><span class="uk-hidden-xsmall"><?php echo GrokBB\Util::getTimespan($announcement->sent, 3) . (($announcement->sent) ? ' ago' : ''); ?></span><span class="uk-visible-xsmall"><?php echo GrokBB\Util::getTimespan($announcement->sent, 1) . (($announcement->sent) ? ' ago' : ''); ?></span></td>
                    <td id="announcement-actions-<?php echo $announcement->id; ?>" class="uk-text-center" nowrap="nowrap">
                        <button id="announcement-verify-<?php echo $announcement->id; ?>" class="uk-button uk-button-danger" style="display: none">Confirm<span class="uk-hidden-xsmall"> Delete</span></button>
                        <button id="announcement-cancel-<?php echo $announcement->id; ?>" class="uk-button uk-button-primary announcement-button" style="display: none">Cancel</button>
                        <button id="announcement-delete-<?php echo $announcement->id; ?>" class="uk-button uk-button-primary" style="<?php echo ($announcement->sent) ? 'display: none' : ''; ?>"><span class="uk-hidden-xsmall">Delete</span><span class="uk-visible-xsmall">Del</span></button>
                        <button id="announcement-edit-<?php echo $announcement->id; ?>" class="uk-button uk-button-primary announcement-button">Edit</button>
                        <button id="announcement-send-<?php echo $announcement->id; ?>" class="uk-button uk-button-primary announcement-button" style="<?php echo ($announcement->sent) ? 'display: none' : ''; ?>">Send</button>
                        <button id="announcement-recall-<?php echo $announcement->id; ?>" class="uk-button uk-button-danger announcement-button" style="<?php echo ($announcement->sent) ? '' : 'display: none'; ?>">Recall</button>
                    </td>
                </tr>
                <tr id="announcement-view-<?php echo $announcement->id; ?>" style="display: none">
                    <td colspan="4"><div id="announcement-content-<?php echo $announcement->id; ?>"></div></td>
                </tr>
                <!-- only here to maintain correct striping -->
                <tr style="display: none"><td></td></tr>
                <?php } ?>
                </table>
                <?php } ?>
            </li>
            <?php } ?>
            <li class="uk-panel uk-panel-box">
                <div class="uk-grid">
                    <div class="uk-width-1-1">
                        You can award users with badges for accomplishing things on your board. The badges are displayed in SVG format, which allows you to animate them if you want.<br />
                        <div class="uk-alert uk-alert-info uk-width-8-10 uk-container-center"><i id="badge-help" class="uk-icon uk-icon-question-circle gbb-icon-large"></i>&nbsp;&nbsp;The SVG image and it's canvas / artboard must be saved with the same width / height you specify in the Badge Dimensions. Otherwise the image will automatically scale, and it may display in a different size than expected, or not at all.</div>
                    </div>
                
                    <div class="uk-width-xlarge-5-10 uk-container-center gbb-spacing-large">
                        <div class="uk-form uk-form-stacked">
                            <div class="uk-form-row">
                                <label class="uk-form-label uk-text-bold" for="badge-desc">Badge Description</label>
                                <div class="uk-text-small gbb-form-help">This text will appear when a someone hovers over the badge. You can enter up to 180 characters.</div>
                                
                                <div class="uk-form-controls">
                                    <input class="uk-width-9-10" type="text" id="badge-desc" maxlength="180">
                                </div>
                            </div>
                            
                            <div class="uk-form-row">
                                <label class="uk-form-label uk-text-bold">Badge Dimensions</label>
                                
                                Width: <input type="text" id="badge-width" maxlength="3" placeholder="60" class="uk-text-center" style="width: 50px"> px
                                &nbsp;&nbsp;
                                Height: <input type="text" id="badge-height" maxlength="3" placeholder="60" class="uk-text-center" style="width: 50px"> px
                            </div>
                            
                            <div class="uk-form-row">    
                                <span class="uk-form-file">
                                    <button class="uk-button uk-button-primary">Upload Badge Image ...</button>
                                    <input id="badge-select" type="file">
                                </span>
                                
                                &nbsp;&nbsp;
                                
                                <span class="uk-alert uk-alert-info uk-text-small uk-hidden-xsmall">
                                    We only accept SVG images and recommend 60x60 pixels.
                                </span>
                                
                                <div id="badge-progressbar" class="uk-progress uk-progress-striped uk-active uk-width-9-10" style="display: none">
                                    <div class="uk-progress-bar" style="width: 0%;"></div>
                                </div>
                                
                                <div id="badge-msg" class="uk-alert uk-alert-danger uk-text-small uk-align-left" style="display: none"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="uk-grid gbb-padding-large" data-uk-grid-margin>
                    <?php foreach ($badges as $badge) { ?>
                    <input id="badge-desc-<?php echo $badge->id; ?>" type="hidden" value="<?php echo str_replace('"', '&quot;', $badge->desc); ?>" />
                    
                    <div class="uk-width-1-3">
                        <div class="uk-grid">
                            <div class="uk-width-2-10">
                                <object data="<?php echo SITE_BASE_URL . '/img.php?bid=' . $board->id . '&bad=' . $badge->id; ?>" type="image/svg+xml" width="<?php echo $badge->width; ?>" height="<?php echo $badge->height; ?>" data-uk-tooltip="{ pos: 'bottom' }" title="<?php echo str_replace('"', '&quot;', $badge->desc); ?>"></object>
                            </div>
                            <div class="uk-width-8-10">
                                <input type="text" id="badge-username-<?php echo $badge->id; ?>" class="uk-width-7-10 uk-align-right" placeholder="Search for a username ...">
                                <button id="badge-user-add-<?php echo $badge->id; ?>" class="uk-button uk-button-primary uk-align-right">Award User</button>
                            </div>
                            
                            <br />
                            
                            <div class="uk-width-1 gbb-spacing">
                                <div class="uk-align-right">
                                    <a class="uk-text-small" id="badge-users-<?php echo $badge->id; ?>"><span id="badge-user-count-<?php echo $badge->id; ?>"><?php echo $badge->counter_users; ?></span> users awarded</a>&nbsp;&nbsp;
                                    <i id="badge-delete" class="uk-icon uk-icon-remove" onclick="deleteBadge('<?php echo $badge->id; ?>', '<?php echo str_replace("'", "\\'", $badge->desc); ?>')" data-uk-tooltip="{ pos: 'top-right' }" title="Delete Badge"></i>&nbsp;
                                    <i id="badge-update" class="uk-icon uk-icon-cog" onclick="updateBadge('<?php echo $badge->id; ?>', '<?php echo str_replace("'", "\\'", $badge->desc); ?>', '<?php echo $badge->width; ?>', '<?php echo $badge->height; ?>')" data-uk-tooltip="{ pos: 'top-right' }" title="Edit Badge"></i>&nbsp;
                                    <span class="uk-form-file"><i id="badge-upload-<?php echo $badge->id; ?>" class="uk-icon uk-icon-image"></i><input id="badge-upload-select-<?php echo $badge->id; ?>" type="file" data-uk-tooltip="{ pos: 'top-right' }" title="Upload New Image"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </li>
        </ul>
    </div>
    <?php require('sidebar.php'); ?>
</div>

<div id="modal-approved-users" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Approved Users</div>
                
                <div id="approved-user-area" style="<?php echo ($approvedUsers) ? '' : 'display: none'; ?>">
                These users are allowed to <?php echo ($board->type == 2) ? 'post' : 'read and post'; ?> topics.
                
                <div class="gbb-spacing-large">
                    <table id="approved-user-table" class="uk-table">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Approved</th>
                        <th class="uk-text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($approvedUsers as $user) { ?>
                    <tr id="approved-user-row-<?php echo $user->id; ?>">
                        <td><a href="<?php echo SITE_BASE_URL . '/user/view/' . $user->id; ?>"><?php echo $user->username; ?></a></td>
                        <td><?php echo \GrokBB\Util::getTimespan($user->approved, 2); ?> ago</td>
                        <td class="uk-text-center"><button id="approved-user-rem-<?php echo $user->id; ?>" class="uk-button uk-button-primary">Remove</button></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                    </table>
                </div>
                
                <br />
                
                </div>
                
                <div id="approved-user-none" class="uk-alert uk-alert-info" style="<?php echo ($approvedUsers) ? 'display: none' : ''; ?>">You do not have any approved users.</div>
                
                <div class="uk-width-1-1 uk-text-center gbb-spacing">
                    <a class="uk-button uk-button-primary uk-modal-close">Close Window</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-moderate-users" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Board Moderators</div>
                
                <div id="moderate-user-area" style="<?php echo ($moderateUsers) ? '' : 'display: none'; ?>">
                These users are allowed to moderate topics and replies.
                
                <div class="gbb-spacing-large">
                    <table id="moderate-user-table" class="uk-table">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Added</th>
                        <th class="uk-text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($moderateUsers as $user) { ?>
                    <tr id="moderate-user-row-<?php echo $user->id; ?>">
                        <td><a href="<?php echo SITE_BASE_URL . '/user/view/' . $user->id; ?>"><?php echo $user->username; ?></a></td>
                        <td><?php echo \GrokBB\Util::getTimespan($user->added, 2); ?> ago</td>
                        <td class="uk-text-center"><button id="moderate-user-rem-<?php echo $user->id; ?>" class="uk-button uk-button-primary">Remove</button></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                    </table>
                </div>
                
                <br />
                
                </div>
                
                <div id="moderate-user-none" class="uk-alert uk-alert-info" style="<?php echo ($moderateUsers) ? 'display: none' : ''; ?>">You do not have any moderators.</div>
                
                <div class="uk-width-1-1 uk-text-center gbb-spacing">
                    <a class="uk-button uk-button-primary uk-modal-close">Close Window</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-banned-users" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Banned Users</div>
                
                <div id="banned-user-area" style="<?php echo ($bannedUsers) ? '' : 'display: none'; ?>">
                These users are not allowed to <?php echo ($board->type == 1) ? 'read' : 'read or post'; ?> topics.
                
                <div class="gbb-spacing-large">
                    <table id="banned-user-table" class="uk-table">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Banned</th>
                        <th class="uk-text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bannedUsers as $user) { ?>
                    <tr id="banned-user-row-<?php echo $user->id; ?>">
                        <td><a href="<?php echo SITE_BASE_URL . '/user/view/' . $user->id; ?>"><?php echo $user->username; ?></a></td>
                        <td><?php echo \GrokBB\Util::getTimespan($user->banned, 2); ?> ago</td>
                        <td class="uk-text-center"><button id="banned-user-rem-<?php echo $user->id; ?>" class="uk-button uk-button-primary">Remove</button></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                    </table>
                </div>
                
                <br />
                
                </div>
                
                <div id="banned-user-none" class="uk-alert uk-alert-info" style="<?php echo ($bannedUsers) ? 'display: none' : ''; ?>">You do not have any banned users.</div>
                
                <div class="uk-width-1-1 uk-text-center gbb-spacing">
                    <a class="uk-button uk-button-primary uk-modal-close">Close Window</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-badge-update" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Badge Description & Dimensions</div>
                
                <div class="uk-form gbb-spacing">
                    <div class="uk-form-row">
                        <label class="uk-form-label uk-text-bold" for="badge-update-desc">Badge Description</label>
                        <div class="uk-text-small gbb-form-help">This text will appear when a someone hovers over the badge. You can enter up to 180 characters.</div>
                        
                        <div class="uk-form-controls">
                            <input class="uk-width-9-10" type="text" id="badge-update-desc" maxlength="180">
                        </div>
                    </div>
                    
                    <div class="uk-form-row">
                        <label class="uk-form-label uk-text-bold">Badge Dimensions</label>
                        
                        <div class="gbb-spacing">
                            Width: <input type="text" id="badge-update-width" maxlength="3" placeholder="60" class="uk-text-center" style="width: 50px"> px
                            &nbsp;&nbsp;
                            Height: <input type="text" id="badge-update-height" maxlength="3" placeholder="60" class="uk-text-center" style="width: 50px"> px
                        </div>
                    </div>
                </div>
                
                <form class="uk-form uk-form-stacked">
                    <div class="uk-form-row uk-align-right gbb-spacing-large">
                        <span id="badge-update-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                        &nbsp;
                        <a id="badge-update-submit" class="uk-button uk-button-primary">Update</a>
                        &nbsp;
                        <a id="badge-update-cancel" class="uk-button uk-button-primary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal-badge-delete" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Confirm Badge Delete</div>
                
                <div class="gbb-spacing">
                Are you sure you want to delete the <span id="badge-delete-desc" class="uk-text-bold"></span> badge?<br /><br />
                All users currently associated to this badge will lose this award.<br />
                </div>
                
                <form class="uk-form uk-form-stacked">
                    <div class="uk-form-row uk-align-right gbb-spacing-large">
                        <a id="badge-delete-confirm" class="uk-button uk-button-danger">Confirm Delete</a>
                        &nbsp;
                        <a id="badge-delete-cancel" class="uk-button uk-button-primary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal-badge-users" class="uk-modal">
    <input id="modal-badge-users-id" type="hidden" value="0" />
    
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Awarded Users</div>
                
                <div id="badge-user-area" style="display: none">
                These users have been awarded the <span id="badge-user-desc" class="uk-text-bold"></span> badge.
                
                <div class="gbb-spacing-large">
                    <table class="uk-table">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Awarded</th>
                        <th class="uk-text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody id="badge-user-tbody">
                    </tbody>
                    </table>
                </div>
                
                <br />
                
                </div>
                
                <div id="badge-user-none" class="uk-alert uk-alert-info">You do not have any users who have been awarded this badge.</div>
                
                <div class="uk-width-1-1 uk-text-center gbb-spacing">
                    <a class="uk-button uk-button-primary uk-modal-close">Close Window</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>