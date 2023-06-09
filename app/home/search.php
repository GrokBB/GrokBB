<?php
require(SITE_BASE_APP . 'header.php');

$text = (isset($_POST['search-text'])) ? GrokBB\Util::sanitizeTXT($_POST['search-text']) : '';

$type = (isset($_POST['search-type'])) ? $_POST['search-type'] : '';;
if (!in_array($type, array('name', 'content', 'both'))) { $type = 'both'; }

$sort = (isset($_POST['search-sort'])) ? $_POST['search-sort'] : '';
if (!in_array($sort, array('rel', 'pop', 'new', 'old', 'ris'))) { $sort = 'rel'; }

$smin = (isset($_POST['search-smin'])) ? (int) $_POST['search-smin'] : '';
$smax = (isset($_POST['search-smax'])) ? (int) $_POST['search-smax'] : '';

$tags = (isset($_POST['search-tags'])) ? GrokBB\Util::sanitizeTXT($_POST['search-tags']) : '';

if ($tags) {
    $_SESSION['tagsForSearch'] = explode(',', $tags);
} else if (isset($_SESSION['tagsForSearch'])){
    unset($_SESSION['tagsForSearch']);
}

$where = array('t.deleted' => 0, 'b.type' => array('<>', 1));

$results = false;

if ($text) {
    switch ($type) {
        case 'name' :
            $matchColumns = 'b.name, b.desc_tagline';
            break;
        case 'content' :
            $matchColumns = 't.title, t.content';
            break;
        case 'both' : default :
            $matchColumns = 'b.name, b.desc_tagline, t.title, t.content';
            break;
    }
    
    $where[$matchColumns] = array('MATCH', $text);
}

if ($smin && $smax) {
    $where['b.counter_users'] = array('BETWEEN ' . $smin . ' AND ' . $smax);
} else if ($smin) {
    $where['b.counter_users'] = array('>=', $smin);
} else if ($smax) {
    $where['b.counter_users'] = array('<=', $smax);
}

if ($tags) {
    $where['b.tag1,b.tag2,b.tag3'] = array('IN', $_SESSION['tagsForSearch']);
}

if ($_POST) {
    switch ($sort) {
        case 'pop' :
            $results = GrokBB\Board::getPop(50, $where);
            break;
        case 'new' :
            $results = GrokBB\Board::getNew(50, $where);
            break;
        case 'old' :
            $results = GrokBB\Board::getOld(50, $where);
            break;
        case 'ris' :
            $results = GrokBB\Board::getRising(50, $where);
            break;
        case 'rel' : default :
            $results = GrokBB\Board::search(50, $where);
            break;
    }
}

$brdsDel = array();

if ($results) {
    foreach ($results as $brd) {
        $brdsDel[$brd->id] = 0;
    }
}

if ($brdsDel) {
    // update the topic counts to exclude deleted ones (this runs faster than modifying the GrokBB\Board::getXXX() queries)
    $topicCounts = $GLOBALS['db']->custom('SELECT id_bd, COUNT(*) AS deleted FROM ' . DB_PREFIX . 'topic WHERE id_bd IN (' . implode(',', array_keys($brdsDel)) . ') AND deleted > 0 GROUP BY id_bd');
    
    if ($topicCounts) {
        foreach ($topicCounts as $tc) {
            if ($results) {
                foreach ($results as $key => $brd) {
                    if ($brd->id == $tc->id_bd) {
                        $results[$key]->counter_topics -= $tc->deleted;
                    }
                }
            }
        }
    }
}
?>

<div class="uk-grid">
    <div class="uk-width-1-1">
        <div class="uk-panel uk-panel-box uk-panel-secondary">
            <h3 class="uk-panel-title uk-text-primary uk-text-bold" data-uk-modal="{ target: '#modal-search-guide', bgclose: false, center: true }" style="cursor: pointer"><span data-uk-tooltip="{ pos: 'right' }" title="Click to View Search Guide">GrokBB Search&nbsp;&nbsp;<i class="uk-icon-question-circle gbb-help-icon"></i>&nbsp;</span></h3>
            
            <form id="search-form" method="post" class="uk-form">
                <div class="uk-grid uk-grid-divider" data-uk-grid-margin>
                    <div class="uk-width-medium-1 uk-width-large-4-10">
                        <input type="text" id="search-text" name="search-text" class="uk-width-1" placeholder="Search for this text in the ..." value="<?php echo str_replace('"', '&quot;', $text); ?>">
                        <br><br>
                        
                        <input type="radio" name="search-type" value="name" <?php echo ($type == 'name') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Board Name
                        &nbsp;&nbsp;
                        <input type="radio" name="search-type" value="content" <?php echo ($type == 'content') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Content
                        &nbsp;&nbsp;
                        <input type="radio" name="search-type" value="both" <?php echo ($type == 'both') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Name & Content
                    </div>
                    <div class="uk-width-medium-1 uk-width-large-6-10">
                        <fieldset>
                            <legend>Sort By</legend>
                            
                            <div class="uk-grid uk-grid-collapse" data-uk-grid-margin>
                                <div class="uk-margin-right">
                                    <input type="radio" name="search-sort" value="rel" <?php echo ($sort == 'rel') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Relevance
                                </div>
                                <div class="uk-margin-right">
                                    <input type="radio" name="search-sort" value="pop" <?php echo ($sort == 'pop') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Popular
                                </div>
                                <div class="uk-margin-right">
                                    <input type="radio" name="search-sort" value="new" <?php echo ($sort == 'new') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Newest First
                                </div>
                                <div class="uk-margin-right">
                                    <input type="radio" name="search-sort" value="old" <?php echo ($sort == 'old') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Oldest First
                                </div>
                                <div class="uk-margin-right">
                                    <input type="radio" name="search-sort" value="ris" <?php echo ($sort == 'ris') ? 'checked="checked"' : ''; ?>>&nbsp;&nbsp;Rising
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                
                <div class="uk-form uk-grid uk-grid-divider" data-uk-grid-margin>
                    <div class="uk-width-medium-1 uk-width-large-4-10">
                        <fieldset>
                            <legend># of Subscribers</legend>
                            
                            <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                                <div class="uk-width-small-3-10 uk-width-large-1-2 uk-width-xlarge-1-3">
                                    <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="width: 35px">Min</td>
                                        <td>
                                            <input type="text" name="search-smin" placeholder="No Minimum" class="uk-form-width-small" value="<?php echo ($smin) ? $smin : ''; ?>">
                                        </td>
                                    </tr>
                                    </table>
                                </div>
                                <div class="uk-width-small-3-10 uk-width-large-1-2 uk-width-xlarge-1-3">
                                    <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="width: 35px">Max</td>
                                        <td>
                                            <input type="text" name="search-smax" placeholder="No Maximum" class="uk-form-width-small" value="<?php echo ($smax) ? $smax : ''; ?>">
                                        </td>
                                    </tr>
                                    </table>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="uk-width-medium-1 uk-width-large-6-10">
                        <fieldset>
                            <legend>Board Tags</legend>
                            
                            <ul id="search-tagbox" class="uk-width-1" placeholder="No Tags"></ul>
                            <input type="hidden" id="search-tags" name="search-tags" value="" />
                        </fieldset>
                    </div>
                </div>
                
                <div class="uk-form uk-grid uk-grid-divider">
                    <div class="uk-width-1-1">
                        <button class="uk-button uk-button-primary uk-align-right" type="button" id="search-submit">Search Boards</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($results) { ?>
<div class="uk-width-1 uk-text-center">
<i class="uk-icon uk-icon-caret-down"></i>
<i class="uk-icon uk-icon-caret-down"></i>
<i class="uk-icon uk-icon-caret-down"></i>
<i class="uk-icon uk-icon-caret-down"></i>
</div>

<div class="uk-panel uk-panel-box uk-panel-header">
    <h3 class="uk-panel-title uk-text-primary uk-text-bold">Matched Boards</h3>
    
    <?php
    $resultsCount = 0;
    
    foreach ($results as $board) {
        $resultsCount++;
    ?>
    <div class="uk-grid" data-uk-grid-margin>
        <div class="uk-width-large-2-6 uk-width-xlarge-1-4">
            <a id="search-link-logo-<?php echo $board->id; ?>" href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $board->name); ?>" data-uk-tooltip="{ pos: 'top-right' }" title="Click to Visit Board">
                <div class="search-header-image" style="background: url('<?php echo SITE_BASE_URL; ?>/img.php?bid=<?php echo $board->id; ?>');"></div>
            </a>
            
            <div class="uk-align-right uk-margin-remove">
                <span class="uk-text-small search-statistic">Users: <strong><?php echo $board->counter_users; ?></strong></span>
                &nbsp;|&nbsp;
                <span class="uk-text-small search-statistic">Topics: <strong><?php echo $board->counter_topics; ?></strong></span>
                &nbsp;|&nbsp;
                <span class="uk-text-small search-statistic">Created <?php echo ltrim(GrokBB\Util::getTimespan($board->created, 1), 0); ?> ago</span>
            </div>
            
            <br />
            
            <div class="gbb-spacing">
                <a id="search-link-name-<?php echo $board->id; ?>" href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $board->name); ?>">
                <?php echo GrokBB\Util::splitLongText($board->name, 10); ?>
                </a>
            </div>
            
            <div class="gbb-spacing-small">
                <?php echo GrokBB\Util::splitLongText($board->desc_tagline, 20); ?>
            </div>
        </div>
        <?php
        if ($type != 'name') {
            if ($type == 'content' || $board->match_1_3 || $board->match_1_4) {
                $matchingTopicIDs = explode(',', $board->topics);
                $matchingTopicCnt = count($matchingTopicIDs);
                
                $matchingTopicsTop5 = array_slice($matchingTopicIDs, 0, 5);
                $matchingTopics = $GLOBALS['db']->getAll('topic', array('id' => array('IN', $matchingTopicsTop5)), 'FIELD(id, ' . implode(',', $matchingTopicsTop5) . ')');
            } else {
                $matchingTopicCnt = 0;
                $matchingTopics = false;
            }
        ?>
        <div class="uk-width-large-4-6 uk-width-xlarge-3-4">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <h3 class="uk-panel-title">
                    <i class="uk-icon-list"></i>&nbsp;&nbsp;Matching Topics:&nbsp;<strong><?php echo $matchingTopicCnt; ?></strong>
                    <?php if ($matchingTopicCnt > 0) { ?>
                    <span class="uk-align-right uk-text-bold uk-text-small">
                        <form action="<?php echo SITE_BASE_URL . '/g/' . str_replace(' ', '_', $board->name) . '/search/sort/qry'; ?>" method="post">
                            <input type="hidden" name="text" value="<?php echo str_replace('"', '&quot;', $text); ?>" />
                            <input type="hidden" name="type" value="both" />
                            <input type="hidden" name="sort" value="rel" />
                            <input type="hidden" name="home" value="1" />
                            
                            <button class="uk-button uk-button-link uk-text-small search-view-all uk-hidden-small"><i class="gbb-menu-icon uk-icon-clone"></i>View All Matching Topics</button>
                            <button class="uk-button uk-button-link uk-text-small search-view-all uk-hidden-medium uk-hidden-large"><i class="gbb-menu-icon uk-icon-clone"></i>View All</button>
                        </form>
                    </span>
                    <?php } ?>
                </h3>
                <?php
                $matchingCount = 0;
                
                if ($matchingTopics) {
                    foreach ($matchingTopics as $topic) {
                        $matchingCount++;
                ?>
                <div class="uk-grid uk-grid-small uk-grid-divider" data-uk-grid-margin>    
                    <div class="uk-width-small-3-10 uk-width-medium-3-10">
                        <h4 data-uk-tooltip="{ pos: 'bottom-left' }" title="Click to View Topic"><a href="<?php echo SITE_BASE_URL . '/g/' . str_replace(' ', '_', $board->name) . '/view/' . $topic->id; ?>"><?php echo $topic->title; ?></a></h4>
                    </div>
                    <div class="uk-width-small-7-10 uk-width-medium-7-10">
                        <div class="uk-alert uk-alert-info" data-uk-alert>
                        <?php
                        // prefix all periods with a space, so that we can find words at the end of sentances
                        $content = str_replace('.', ' .', strip_tags($topic->content));
                        
                        $eachPosition = GrokBB\Util::highlightText($content, $text);
                        
                        if ($eachPosition) {
                            $pad = 100;
            
                            $matchString = '';
                            
                            $pre = $eachPosition[0]['pos'] - $pad;
                            $add = $pad;
                            
                            if ($pre < 0) {
                                $add = $pad + $pre;
                                $pre = 0;
                            }
                            
                            // prefix
                            $matchString .= substr($content, $pre, $add);
                            
                            // highlight
                            $matchString .= '<mark>' . substr($content, $eachPosition[0]['pos'], $eachPosition[0]['len']) . '</mark>';
                            
                            // append
                            $matchString .= substr($content, $eachPosition[0]['pos'] + $eachPosition[0]['len'], $pad);
                            
                            // add the ellipses and restore our periods
                            echo '... ' . str_replace(' .', '.', $matchString) . ' ...';
                        } else {
                            echo 'No Matching Content';
                        }
                        ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($matchingCount != count($matchingTopics)) { ?>
                <hr class="uk-grid-divider" />
                <?php } } } ?>
            </div>
        </div>
        <?php } ?>
    </div>
    
    <?php if ($resultsCount != count($results)) { ?>
    <hr class="uk-grid-divider" />
    <?php } } ?>
</div>
<?php } else if ($_POST) { ?>
<div class="uk-alert uk-alert-danger">Your search returned no boards.</div>
<?php } ?>

<?php require(SITE_BASE_APP . 'footer.php'); ?>