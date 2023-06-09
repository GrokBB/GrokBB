<?php
if (isset($_SESSION['user'])) {
    header('Location: ' . SITE_BASE_URL);
}

$hasToken = $_SESSION['objectid'] && ctype_xdigit($_SESSION['objectid']);

if ($hasToken) {
    $reset = $GLOBALS['db']->getOne('user_reset', array('token' => $_SESSION['objectid'], 'used' => 0));
    
    if ($reset == false) {
        header('Location: ' . SITE_BASE_URL . '/user/reset');
    }
}

require(SITE_BASE_APP . 'header.php');
?>

<div class="uk-panel uk-panel-box">
    <h3 class="uk-panel-title uk-text-primary uk-text-bold">Password Reset</h3>
    
    <div class="uk-grid <?php echo ($hasToken) ? '' : 'uk-container'; ?>">
        <div class="<?php echo ($hasToken) ? 'uk-width-1-1' : 'uk-width-small-8-10 uk-width-large-4-6 uk-width-xlarge-2-3 uk-container-center'; ?>">
            <?php
            if ($hasToken) {
                $success = false;
                
                if (time() > ($reset->sent + 86400)) {
                    $message = 'This link has expired. Please <a href="' . SITE_BASE_URL . '/user/reset">reset</a> your password again.';
                } else {
                    // bin2hex will double the string length
                    $newPassword = bin2hex(openssl_random_pseudo_bytes(10));
                    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    
                    $update = $GLOBALS['db']->update('user', array('password' => $newPasswordHash, 'id' => $reset->id_ur));
    	            
    	            if ($update) {
    	                $GLOBALS['db']->update('user_reset', array('used' => time(), 'id' => $reset->id));
    	                
    	                $message = 'Your new password is: ' . $newPassword;
    	                $success = true;
    	            } else {
    	                $message = 'There has been an error with resetting your password. Please contact us at ' .
    	                       '<a href="mailto:admin@grokbb.com?subject=Password Reset Error (' . $_SESSION['objectid'] . ')">admin@grokbb.com</a>';
    	            }
                }
                
                echo '<div class="uk-alert ' . (($success) ? 'uk-alert-info' : 'uk-alert-danger') . ' gbb-spacing">' . $message . '</span><br />';
            } else {
            ?>
            <div class="uk-form uk-form-stacked gbb-spacing">
                <div class="uk-form-row uk-align-right">
                    <div class="uk-text-small gbb-form-help">
                        Please enter your username or email address, and we will send you a link to reset your password.
                    </div>
                    <div class="uk-form-controls">
                        <input class="uk-width-1-1" type="text" placeholder="John Smith 123 or username@domain.com" id="reset-username" maxlength="255" autocomplete="off">
                        <div class="uk-alert uk-text-small"><i class="uk-icon-envelope"></i>&nbsp;&nbsp;You must have an email address associated to your account; otherwise, this will not work.</div>
                        <br />
                        <span id="reset-username-msg" class="uk-alert uk-text-small" style="display: none"></span>
                        &nbsp;<a id="reset-send" class="uk-button uk-button-primary uk-align-right">Send Reset Link</a>
                        <br />
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>