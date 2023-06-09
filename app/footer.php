</div>

<div class="uk-block uk-block-secondary uk-text-center uk-margin-top gbb-footer gbb-padding-large">
    <br />
    Copyright &copy; <?php echo date('Y'); ?> GrokBB, LLC
    <br />
    
    <div class="gbb-spacing-small">
        <a href="<?php echo SITE_BASE_URL; ?>/home/terms">User Agreement</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <a href="<?php echo SITE_BASE_URL; ?>/home/privacy">Privacy Policy</a>
    </div>
    
    <br />
</div>

<?php require(SITE_BASE_APP . 'modals.php'); ?>

<!-- <script src="<?php echo SITE_BASE_URL; ?>/lib/arrowchat/external.php?type=djs"></script> -->
<!-- <script src="<?php echo SITE_BASE_URL; ?>/lib/arrowchat/external.php?type=js"></script> -->
<script src="<?php echo SITE_BASE_URL; ?>/lib/grokbb/js/grokbb.js.php"></script>

<?php
if (DEBUG && isset($GLOBALS['db'])) {
    $profile = $GLOBALS['db']->query('SHOW PROFILES');
    $GLOBALS['db']->query('SET PROFILING=0');

    $queries = $profile->fetchAll(\PDO::FETCH_OBJ);
    if (!$queries) { $queries = array(); }
    
    $durationTotal = 0;
    
    foreach ($queries as $query) {
        $durationTotal += $query->Duration;
    }
    ?>
    <div class="gbb-padding">
        <div class="uk-panel uk-panel-box gbb-padding-large">
            <h4>
                GrokBB Executed <span class="uk-text-bold"><?php echo count($queries); ?></span> Queries 
                in <span class="uk-text-bold"><?php echo $durationTotal; ?></span> Seconds
            </h4>
            
            <table class="uk-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Duration</th>
                        <th>Query</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($queries as $query) { ?>
                    <tr>
                        <td><?php echo $query->Query_ID; ?></td>
                        <td><?php echo $query->Duration; ?></td>
                        <td>
                            <?php echo $GLOBALS['db']->log[$query->Query_ID - 1]; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
?>

</body>
</html>