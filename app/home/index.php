<?php
require(SITE_BASE_APP . 'header.php');

$brdsDel = array();

// popular

$brdsPop = GrokBB\Board::getPop(10);

if ($brdsPop) {
    foreach ($brdsPop as $pop) {
        $brdsDel[$pop->id] = 0;
    }
}

// new

$brdsNew = GrokBB\Board::getNew(10);

if ($brdsNew) {
    foreach ($brdsNew as $new) {
        $brdsDel[$new->id] = 0;
    }
}

// rising

$brdsRis = GrokBB\Board::getRising(10);

if ($brdsRis) {
    foreach ($brdsRis as $ris) {
        $brdsDel[$ris->id] = 0;
    }
}

// random

$brdsRan = GrokBB\Board::getRandom(10);

if ($brdsRan) {
    foreach ($brdsRan as $ran) {
        $brdsDel[$ran->id] = 0;
    }
}

if ($brdsDel) {
    // update the topic counts to exclude deleted ones (this runs faster than modifying the GrokBB\Board::getXXX() queries)
    $topicCounts = $GLOBALS['db']->custom('SELECT id_bd, COUNT(*) AS deleted FROM ' . DB_PREFIX . 'topic WHERE id_bd IN (' . implode(',', array_keys($brdsDel)) . ') AND deleted > 0 GROUP BY id_bd');
    
    if ($topicCounts) {
        foreach ($topicCounts as $tc) {
            // popular
            if ($brdsPop) {
                foreach ($brdsPop as $key => $pop) {
                    if ($pop->id == $tc->id_bd) {
                        $brdsPop[$key]->counter_topics -= $tc->deleted;
                    }
                }
            }
            
            // new
            if ($brdsNew) {
                foreach ($brdsNew as $key => $new) {
                    if ($new->id == $tc->id_bd) {
                        $brdsNew[$key]->counter_topics -= $tc->deleted;
                    }
                }
            }
            
            // rising
            if ($brdsRis) {
                foreach ($brdsRis as $key => $ris) {
                    if ($ris->id == $tc->id_bd) {
                        $brdsRis[$key]->counter_topics -= $tc->deleted;
                    }
                }
            }
            
            // random
            if ($brdsRan) {
                foreach ($brdsRan as $key => $ran) {
                    if ($ran->id == $tc->id_bd) {
                        $brdsRan[$key]->counter_topics -= $tc->deleted;
                    }
                }
            }
        }
    }
}
?>

<div class="uk-grid">
    <div class="uk-width-1-1 uk-hidden-small uk-hidden-medium">
        <div class="uk-panel uk-panel-box">
            <div id="gbb-welcome gbb-padding">
            Welcome to <b>GrokBB</b>, a place for contemplation, discussion and community building!<span class="uk-margin-small-left">If you're new here then please take the <a href="<?php echo SITE_BASE_URL; ?>/home/tour" class="uk-button uk-button-primary">Quick Tour</a>&nbsp;.</span>
            <div class="uk-margin-top"><a href="<?php echo SITE_BASE_URL; ?>/home/search" class="uk-button uk-button-primary">Search</a> for something that interests you, browse the boards, or <a href="<?php echo SITE_BASE_URL; ?>/home/create" class="uk-button uk-button-primary">Create Your Own Community</a>&nbsp;.</div>
            </div>
        </div>
    </div>
    <div class="uk-width-1-1 uk-hidden-large">
        <div class="uk-panel uk-panel-box">
            <div id="gbb-welcome gbb-padding">
            Welcome to <b>GrokBB</b>, a place for contemplation, discussion and community building!
            </div>
            
            <br />
            
            <div id="gbb-welcome gbb-padding">
            <a href="<?php echo SITE_BASE_URL; ?>/home/tour" class="uk-button uk-button-primary">Quick Tour</a>
            &nbsp;|&nbsp;
            <div class="uk-hidden-small" style="display: inline">
            <a href="<?php echo SITE_BASE_URL; ?>/home/search" class="uk-button uk-button-primary">Search</a>
            &nbsp;|&nbsp;
            </div>
            <a href="<?php echo SITE_BASE_URL; ?>/home/create" class="uk-button uk-button-primary">Create Your Own Community</a>
            </div>
        </div>
    </div>
</div>

<div class="uk-grid uk-grid-medium uk-grid-match gbb-spacing" data-uk-grid-match="{target:'.uk-panel'}" data-uk-grid-margin>
    <div class="uk-width-small-1-2 uk-width-large-1-3 uk-width-xlarge-1-4">
        <div class="uk-panel uk-panel-box uk-panel-header">
            <h3 class="uk-panel-title">
                🍿&nbsp;&nbsp;Popular
                <span class="uk-align-right uk-text-bold uk-text-small gbb-top100">
                    <a href="<?php echo SITE_BASE_URL; ?>/home/top100/pop"><i class="gbb-menu-icon uk-icon-clone"></i>View Top 100</a>
                </span>
            </h3>
            
            <table class="uk-table uk-table-hover uk-table-condensed">
            <tr>
                <th>Board Name</th>
                <th class="uk-text-right">Users</th>
                <th class="uk-text-right">Topics</th>
            </tr>
            <?php foreach ($brdsPop as $board) { ?>
            <tr>
                <td class="uk-text-bold gbb-boards-td">
                    <a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $board->name); ?>"><?php echo GrokBB\Util::splitLongText($board->name, 10); ?></a>
                </td>
                <td class="uk-text-right gbb-boards-td"><?php echo $board->counter_users; ?></td>
                <td class="uk-text-right gbb-boards-td"><?php echo $board->counter_topics; ?></td>
            </tr>
            <tr>
                <td class="uk-text-small" colspan="3"><?php echo GrokBB\Util::splitLongText($board->desc_tagline, 25); ?></td>
            </tr>
            <?php } ?>
            </table>
        </div>
    </div>
    
    <div class="uk-width-small-1-2 uk-width-large-1-3 uk-width-xlarge-1-4">
        <div class="uk-panel uk-panel-box uk-panel-header">
            <h3 class="uk-panel-title">
                🎂&nbsp;&nbsp;New
                <span class="uk-align-right uk-text-bold uk-text-small gbb-top100">
                    <a href="<?php echo SITE_BASE_URL; ?>/home/top100/new"><i class="gbb-menu-icon uk-icon-clone"></i>View Top 100</a>
                </span>
            </h3>
            
            <table class="uk-table uk-table-hover uk-table-condensed">
            <tr>
                <th>Board Name</th>
                <th class="uk-text-right">Users</th>
                <th class="uk-text-right">Topics</th>
            </tr>
            <?php foreach ($brdsNew as $board) { ?>
            <tr>
                <td class="uk-text-bold gbb-boards-td"><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $board->name); ?>"><?php echo GrokBB\Util::splitLongText($board->name, 10); ?></a></td>
                <td class="uk-text-right gbb-boards-td"><?php echo $board->counter_users; ?></td>
                <td class="uk-text-right gbb-boards-td"><?php echo $board->counter_topics; ?></td>
            </tr>
            <tr>
                <td class="uk-text-small" colspan="3"><?php echo GrokBB\Util::splitLongText($board->desc_tagline, 25); ?></td>
            </tr>
            <?php } ?>
            </table>
        </div>
    </div>
    
    <div class="uk-width-small-1-2 uk-width-large-1-3 uk-width-xlarge-1-4">
        <div class="uk-panel uk-panel-box uk-panel-header">
            <h3 class="uk-panel-title">
                🚀&nbsp;&nbsp;Rising
                <span class="uk-align-right uk-text-bold uk-text-small gbb-top100">
                    <a href="<?php echo SITE_BASE_URL; ?>/home/top100/rising"><i class="gbb-menu-icon uk-icon-clone"></i>View Top 100</a>
                </span>
            </h3>
            
            <table class="uk-table uk-table-hover uk-table-condensed">
            <tr>
                <th>Board Name</th>
                <th class="uk-text-right">Users</th>
                <th class="uk-text-right">Topics</th>
            </tr>
            <?php foreach ($brdsRis as $board) { ?>
            <tr>
                <td class="uk-text-bold gbb-boards-td"><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $board->name); ?>"><?php echo GrokBB\Util::splitLongText($board->name, 10); ?></a></td>
                <td class="uk-text-right gbb-boards-td"><?php echo $board->counter_users; ?></td>
                <td class="uk-text-right gbb-boards-td"><?php echo $board->counter_topics; ?></td>
            </tr>
            <tr>
                <td class="uk-text-small" colspan="3"><?php echo GrokBB\Util::splitLongText($board->desc_tagline, 25); ?></td>
            </tr>
            <?php } ?>
            </table>
        </div>
    </div>
    
    <div class="uk-width-small-1-2 uk-width-large-1-1 uk-width-xlarge-1-4">
        <div class="uk-panel uk-panel-box uk-panel-header">
            <h3 class="uk-panel-title">
                🔀&nbsp;&nbsp;Random
                <span class="uk-align-right uk-text-bold uk-text-small gbb-top100">
                    <a href="<?php echo SITE_BASE_URL; ?>/home/top100/random"><i class="gbb-menu-icon uk-icon-clone"></i>View 100</a>
                </span>
            </h3>
            
            <table class="uk-table uk-table-hover uk-table-condensed">
            <tr>
                <th>Board Name</th>
                <th class="uk-text-right">Users</th>
                <th class="uk-text-right">Topics</th>
            </tr>
            <?php foreach ($brdsRan as $board) { ?>
            <tr>
                <td class="uk-text-bold gbb-boards-td"><a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $board->name); ?>"><?php echo GrokBB\Util::splitLongText($board->name, 10); ?></a></td>
                <td class="uk-text-right gbb-boards-td"><?php echo $board->counter_users; ?></td>
                <td class="uk-text-right gbb-boards-td"><?php echo $board->counter_topics; ?></td>
            </tr>
            <tr>
                <td class="uk-text-small" colspan="3"><?php echo GrokBB\Util::splitLongText($board->desc_tagline, 25); ?></td>
            </tr>
            <?php } ?>
            </table>
        </div>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>