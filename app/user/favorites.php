<?php
if (!isset($_SESSION['user'])) {
    header('Location: ' . SITE_BASE_URL);
}

require(SITE_BASE_APP . 'header.php');

$fvBoards = $GLOBALS['db']->getAll('board b INNER JOIN ' . DB_PREFIX . 'user_board ub ON b.id = ub.id_bd', array('ub.id_ur' => $_SESSION['user']->id, 'deleted' => 0), 'b.name ASC', array('b.*', 'ub.added'));
?>

<div id="favorites" class="uk-panel uk-panel-box">
    <div class="uk-panel-badge uk-badge uk-badge-success"><?php echo count($fvBoards); ?></div>
    <h3 class="uk-panel-title uk-text-primary uk-text-bold">Favorite Boards</h3>
    
    <?php if ($fvBoards) { ?><br /><?php } ?>
    
    <div class="uk-grid" data-uk-grid-margin>
        <?php if ($fvBoards) { ?>
            <?php foreach ($fvBoards as $fvBoard) { ?>
            <div class="uk-width-1-4">
                <a href="<?php echo SITE_BASE_URL; ?>/g/<?php echo str_replace(' ', '_', $fvBoard->name); ?>" data-uk-tooltip="{ pos: 'top-right' }" title="Click to Visit Board">
                <div class="favorite-header-image" style="background: url('<?php echo SITE_BASE_URL; ?>/img.php?bid=<?php echo $fvBoard->id; ?>');"></div>
                <?php echo GrokBB\Util::splitLongText($fvBoard->name, 10); ?></a>
                &nbsp;|&nbsp;
                <span class="uk-text-small favorite-added">Added <?php echo ltrim(GrokBB\Util::getTimespan($fvBoard->added, 1), 0); ?> ago</span>
                <i id="favorite-remove-<?php echo $fvBoard->id; ?>" class="uk-icon uk-icon-remove uk-align-right" data-uk-tooltip="{ pos: 'left' }" title="Remove Favorite"></i>
                <div class="gbb-spacing-small"><?php echo GrokBB\Util::splitLongText($fvBoard->desc_tagline, 20); ?></div>
            </div>
            <?php } ?>
        <?php } else { ?>
            <div class="uk-width-1-1">
                <div class="uk-alert uk-alert-info" data-uk-alert>
                    You have no favorite boards.
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>