<?php header('Content-Type: application/javascript'); ?>
<?php require_once('../../../cfg.php'); ?>

/* GrokBB */

$('.gbb-header').click(function() {
    <?php if (substr($_SESSION['pagerqst'], 0, 1) != 'g') { ?>
    window.location.href = '<?php echo SITE_BASE_URL; ?>';
    <?php } else { ?>
    window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>';
    <?php } ?>
});

$('[id*="logout"]').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'User',
               method: 'logout'
          }
    }).done(function(data) {
        window.location.href = '/';
    });
});

if (window.location.hash != '') {
    switch (window.location.hash) {
        case '#success' :
            UIkit.notify('Your updates have been saved.', { status: 'info' });
            break;
    }
}

var spectrumOptions = {
    showInput: true,
    cancelText: 'Cancel',
    chooseText: 'Apply',
    preferredFormat: 'hex'
}

var chartJsLineOptions = {
    backgroundColor: 'rgba(255,99,132,0.2)',
    borderColor: 'rgba(255,99,132,1)',
    borderWidth: 1,
    hoverBackgroundColor: 'rgba(255,99,132,0.4)',
    hoverBorderColor: 'rgba(255,99,132,1)'
}

$(document).ready(function() {
    $('.tagInputField').attr('placeholder', 'Enter Your Tags ...');
    $('.tagHandler ul.tagHandlerContainer li.tagItem').attr('data-uk-tooltip', '{ pos: "bottom-left" }');
    $('.tagHandler ul.tagHandlerContainer li.tagItem').attr('title', 'Click to Delete Tag');
});

var keepAlive = false;

function keepAliveStart() {
    // keep the current session alive every 20 minutes until stopped (the PHP timeout is 24 minutes)
    // this should only be used when necessary, since it creates a security risk
    // i.e. it should only be run when someone is typing in an editor
    keepAlive = setInterval(function() {
        $.ajax({ url: '<?php echo SITE_BASE_URL; ?>/cfg.php'});
    }, 1200000)
}

function keepAliveClose() {
    if (keepAlive != false) {
        clearInterval(keepAlive);
        keepAlive = false;
    }
}

<?php
require('grokbb.modals.js.php');

$templateJS = str_replace('.php', '.js.php', $_SESSION['template']);
$templateJS = str_replace(SITE_BASE_APP, SITE_BASE_GBB . 'js' . DIRECTORY_SEPARATOR, $templateJS);

$sepPos = strrpos($templateJS, DIRECTORY_SEPARATOR) + 1;
$dotPos = strpos($templateJS, '.');

// include the JS for the common header
$headerJS = substr($templateJS, 0, $sepPos) . 'header' . substr($templateJS, $dotPos);
if (is_readable($headerJS)) { require($headerJS); }

// include the JS for the template
if (is_readable($templateJS)) { require($templateJS); }

// include the JS for the common sidebar
$sidebarJS = substr($templateJS, 0, $sepPos) . 'sidebar' . substr($templateJS, $dotPos);
if (is_readable($sidebarJS)) { require($sidebarJS); }
?>