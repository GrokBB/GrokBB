<?php
if (!isset($_SESSION['user']) || $_SESSION['user']->id != 1) {
    header('Location: ' . SITE_BASE_URL);
}

$GLOBALS['displayHeader'] = false;

require(SITE_BASE_APP . 'header.php');

if (isset($_POST['clear']) && $_POST['clear']) {
    $GLOBALS['db']->delete('error');
}

$errors = $GLOBALS['db']->getAll('error', false, 'time DESC');
?>

<div class="uk-grid">
    <div class="uk-width-1-1">
        <div class="uk-panel uk-panel-box uk-panel-secondary">
            <h3 class="uk-panel-title uk-text-primary uk-text-bold">
                <form action="<?php echo SITE_BASE_URL; ?>/admin/errors" method="post">
                <input type="hidden" name="clear" value="1" />
                PHP Error Log<button class="uk-button uk-button-primary uk-align-right">Clear Log</button>
                </form>
            </h3>
            
            <?php if ($errors) { ?>
            <table class="uk-table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Line</th>
                    <th>File</th>
                    <th>Message</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($errors as $error) { ?>
                <tr>
                    <td><?php echo date('Y-m-d h:i:sa T', $error->time); ?></td>
                    <td>
                    <?php
                        switch($error->level) {
                            case E_ERROR:                   // 1
                                echo 'E_ERROR';
                                break;
                            case E_WARNING:                 // 2
                                echo 'E_WARNING';
                                break;
                            case E_PARSE:                   // 4
                                echo 'E_PARSE';
                                break;
                            case E_NOTICE:                  // 8
                                echo 'E_NOTICE';
                                break;
                            case E_CORE_ERROR:              // 16
                                echo 'E_CORE_ERROR';
                                break;
                            case E_CORE_WARNING:            // 32
                                echo 'E_CORE_WARNING';
                                break;
                            case E_COMPILE_ERROR:           // 64
                                echo 'E_COMPILE_ERROR';
                                break;
                            case E_COMPILE_WARNING:         // 128
                                echo 'E_COMPILE_WARNING';
                                break;
                            case E_USER_ERROR:              // 256
                                echo 'E_USER_ERROR';
                                break;
                            case E_USER_WARNING:            // 512
                                echo 'E_USER_WARNING';
                                break;
                            case E_USER_NOTICE:             // 1024
                                echo 'E_USER_NOTICE';
                                break;
                            case E_STRICT:                  // 2048
                                echo 'E_STRICT';
                                break;
                            case E_RECOVERABLE_ERROR:       // 4096
                                echo 'E_RECOVERABLE_ERROR';
                                break;
                            case E_DEPRECATED:              // 8192
                                echo 'E_DEPRECATED';
                                break;
                            case E_USER_DEPRECATED:         // 16384
                                echo 'E_USER_DEPRECATED';
                                break;
                        } 
                    ?>
                    </td>
                    <td><?php echo $error->line; ?></td>
                    <td><?php echo $error->file; ?></td>
                    <td><?php echo $error->message; ?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
            <div class="uk-alert uk-alert-info">There are no PHP errors logged.</div>
            <?php } ?>
        </div>
    </div>
</div>