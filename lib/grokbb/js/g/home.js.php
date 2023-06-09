/* G > Home */

<?php
if (($_SESSION['board']->type == 1 && \GrokBB\Board::isApproved($_SESSION['board']->id) == false) || 
    (isset($_SESSION['user']) && $_SESSION['user']->isBanned)) {
?>

$('#request-access-submit').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    if ($('#editor-request-access').val() == '') {
        UIkit.notify('You must enter a message.', { status: 'info' });
    }
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'requestBoardAccess',
               params: {
                       bid: '<?php echo $_SESSION['board']->id; ?>',
                   message: $('#editor-request-access').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            keepAliveClose();
            
            $('#request-frm').hide();
            $('#request-msg').show();
        }
    });
    <?php } ?>
});

var codemirror = UIkit.htmleditor($('#editor-request-access'), { markdown: true, height: 200 });
$('#editor-request-access-char').text(codemirror.editor.doc.getValue().length);

codemirror.editor.doc.on('change', function(arr) {
    var charLength = codemirror.editor.doc.getValue().length;
    $('#editor-request-access-char').text(charLength);
    
    if (charLength > 15000) {
        $('#editor-request-access-char').addClass('uk-text-danger');
    } else {
        $('#editor-request-access-char').removeClass('uk-text-danger');
    }
    
    if (keepAlive == false) {
        keepAliveStart();
    }
});

<?php } else { ?>

<?php if (isset($_SESSION['user']) && $_SESSION['user']->isOwner == $_SESSION['board']->id) { ?>
    <?php if (($_SESSION['board']->stripe_id === '0' || $_SESSION['board']->stripe_cancelled > 0) && time() > $_SESSION['board']->expires && $_SESSION['board']->plan != 4) { ?>
    UIkit.modal('#modal-subscription').show();
    <?php } else if ($_SESSION['board']->updated == 0) { ?>
    UIkit.modal('#modal-welcome').show();
    <?php } ?>
<?php } ?>

<?php if ($_SESSION['board']->isArchived) { ?>

$('[id*="archive-plan-"]').click(function() {
    $('#' + this.id + '-radio').prop('checked', true);
});

/* Stripe */

var handler = StripeCheckout.configure({
    key: '<?php echo (CC_LIVE) ? CC_LIVE_PK : CC_TEST_PK; ?>',
    locale: 'auto',
    token: function(token) {
        $.ajax({
            method: 'POST',
               url: '<?php echo SITE_BASE_URL; ?>/api.php',
              data: {
                  apihash: '<?php echo $_SESSION['apihash']; ?>',
                   object: 'Board',
                   method: 'subscribeArchived',
                   params: {
                          id: $('#board-id').val(),
                       token: token,
                        plan: $('input[name="archive-plan"]:checked').val()
                   }
              }
        }).done(function(data) {
            $('#archive-msg').hide();
            
            var data = $.parseJSON(data);
            
            if (data.result) {
                UIkit.notify('You have been subscribed successfully.', { status: 'info' });
                
                setTimeout(function() {
                    window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings';
                }, 1000);
            } else {
                $('#archive-msg').html(data.msg);
                $('#archive-msg').show();
            }
        });
    }
});

$('#archive-plan-stripe').on('click', function(e) {
    <?php if (CC_TURN_ON == 0) { ?>
    UIkit.notify('This feature is currently disabled.', { status: 'info' });
    <?php } else if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    if ($('#archive-plan-monthly-radio').is(':checked')) {
        var stripeDescription = 'Monthly Subscription ($3 per month)';
    } else {
        var stripeDescription = 'Yearly Subscription ($30 per year)';
    }
    
    handler.open({
        name: 'GrokBB',
        description: stripeDescription,
        panelLabel: 'Subscribe'
    });
    
    e.preventDefault();
    <?php } ?>
});

$(window).on('popstate', function() {
    handler.close();
});

<?php } ?>

$('[id*="topic-user-"]').click(function(e) {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    if ($(e.target).hasClass('uk-icon-shield')) {
        displayBadges(uid);
    } else {
        window.location.href = '<?php echo SITE_BASE_URL; ?>/user/view/' + uid;
    }
});

$('[id*="topic-save-"]').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    var tid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'saveForLater',
               params: {
                    tid: tid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('#topic-save-' + tid).hide();
            $('#topic-saved-' + tid).show();
        }
    });
    <?php } ?>
});

$('[id*="topic-unsave-"]').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    var tid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'unsaveForLater',
               params: {
                    tid: tid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('#topic-save-' + tid).show();
            $('#topic-saved-' + tid).hide();
        }
    });
    <?php } ?>
});

function addTag(tid, tag) {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    return false;
    <?php } else { ?>
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'addTag',
               params: {
                    tid: tid,
                   name: tag
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('.topic-tags .tagHandler ul.tagHandlerContainer li.tagItem').attr('data-uk-tooltip', '{ pos: "bottom-left" }');
            $('.topic-tags .tagHandler ul.tagHandlerContainer li.tagItem').attr('title', 'Click to Delete Tag');
            
            // TODO: add a new method to TagHandler that will allow you to update the availableTags,
            //       and this will allow us to keep the autocomplete dropdowns in sync 
            //       without multiple trips to the server, which is required by getURL
            
            return true;
        }
    });
    <?php } ?>
}

function delTag(tid, tag) {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'delTag',
               params: {
                    tid: tid,
                   name: tag
               }
          }
    });
}

<?php foreach ($_SESSION['topics'] as $tid) { ?>
var tagHandlerSettings = {
    assignedTags: tagsByTopic[<?php echo $tid; ?>],
           onAdd: function(tag) { return addTag(<?php echo $tid; ?>, tag); },
        onDelete: function(tag) {
            if (!tagsModerator[<?php echo $tid; ?>] || !tagsModerator[<?php echo $tid; ?>][tag]) {
                delTag(<?php echo $tid; ?>, tag);
                return true;
            } else {
                UIkit.notify('This tag was added by a moderator.<br />It can not be deleted by you.', { status: 'info' });
                return false;
            }
        }
}

jQuery.extend(tagHandlerSettings, tagHandlerDefaults);

$('#topic-tags-<?php echo $tid; ?>').tagHandler(tagHandlerSettings);
<?php } ?>

<?php } // private board ?>