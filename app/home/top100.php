<?php
require(SITE_BASE_APP . 'header.php');

switch ($_SESSION['objectid']) {
    case 'new' :
        $brds100 = GrokBB\Board::getNew(100);
        $title = 'New';
        break;
    case 'rising' :
        $brds100 = GrokBB\Board::getRising(100);
        $title = 'Rising';
        break;
    case 'random' :
        $brds100 = GrokBB\Board::getRandom(100);
        $title = 'Random';
        break;
    case 'pop' : default :
        $brds100 = GrokBB\Board::getPop(100);
        $title = 'Popular';
        break;
}

$brdsDel = array();

if ($brds100) {
    foreach ($brds100 as $brd) {
        $brdsDel[$brd->id] = 0;
    }
}

if ($brdsDel) {
    // update the topic counts to exclude deleted ones (this runs faster than modifying the GrokBB\Board::getXXX() queries)
    $topicCounts = $GLOBALS['db']->custom('SELECT id_bd, COUNT(*) AS deleted FROM ' . DB_PREFIX . 'topic WHERE id_bd IN (' . implode(',', array_keys($brdsDel)) . ') AND deleted > 0 GROUP BY id_bd');
    
    if ($topicCounts) {
        foreach ($topicCounts as $tc) {
            if ($brds100) {
                foreach ($brds100 as $key => $brd) {
                    if ($brd->id == $tc->id_bd) {
                        $brds100[$key]->counter_topics -= $tc->deleted;
                    }
                }
            }
        }
    }
}
?>

<div id="top100" class="uk-panel uk-panel-box">
    <div class="uk-panel-badge uk-badge uk-badge-success">100</div>
    <h3 class="uk-panel-title uk-text-primary uk-text-bold"><?php echo $title; ?> Boards</h3>
    
    <div class="uk-grid uk-grid" data-uk-grid-margin>
        <?php foreach ($brds100 as $board) { ?>
        <div class="uk-width-small-1-2 uk-width-large-1-3 uk-width-xlarge-1-4" data-uk-tooltip="{ pos: 'top-right' }" title="Click to Visit Board">
            <a id="top100-link-logo-<?php echo $board->id; ?>" href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $board->name); ?>">
                <div class="top100-header-image" style="background: url('<?php echo SITE_BASE_URL; ?>/img.php?bid=<?php echo $board->id; ?>');"></div>
            </a>
            
            <div class="uk-align-right uk-margin-remove">
                <span class="uk-text-small top100-statistic">Users: <strong><?php echo $board->counter_users; ?></strong></span>
                &nbsp;|&nbsp;
                <span class="uk-text-small top100-statistic">Topics: <strong><?php echo $board->counter_topics; ?></strong></span>
                &nbsp;|&nbsp;
                <span class="uk-text-small top100-statistic">Created <?php echo ltrim(GrokBB\Util::getTimespan($board->created, 1), 0); ?> ago</span>
            </div>
            
            <br />
            
            <div class="gbb-spacing">
                <a id="top100-link-name-<?php echo $board->id; ?>" href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $board->name); ?>">
                <?php echo GrokBB\Util::splitLongText($board->name, 10); ?>
                </a>
            </div>
            
            <div class="gbb-spacing-small">
                <?php echo GrokBB\Util::splitLongText($board->desc_tagline, 20); ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>