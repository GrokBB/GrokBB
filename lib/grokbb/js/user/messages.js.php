/* User > Messages */

if (window.location.hash != '') {
    var activeTab = $(window.location.hash);
    
    if (activeTab) {
        $(window.location.hash).addClass('uk-active');
        $(window.location.hash).attr('id', '#');
    }
}

/* Message Inbox */

$('[id*="messages-delete-inbox-"]').click(function() {
    var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $('#messages-delete-inbox-' + mid).hide();
    $('#messages-reply-inbox-' + mid).hide();
    
    $('#messages-verify-inbox-' + mid).show();
    $('#messages-cancel-inbox-' + mid).show();
});

$('[id*="messages-verify-inbox-"]').click(function() {
    var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Message',
               method: 'deleteRCVD',
               params: { id: mid }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your message has been deleted.', { status: 'info' });
            
            // NOTE: we would have to detect when all the rows in the current page have been deleted
            //       and only then do the page refresh (the timeout below is much easier to implement)
            // $('#messages-row-' + mid).remove();
            
            setTimeout(function() {
                $('#messages-inbox').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/user/messages/#messages-inbox';
                if (activeTab && <?php echo (int) empty($_SESSION['objectid']); ?>) { window.location.reload(true); }
            }, 1000);
        } else {
            $('#messages-verify-inbox-' + mid).hide();
            $('#messages-cancel-inbox-' + mid).hide();
            
            $('#messages-delete-inbox-' + mid).show();
            $('#messages-reply-inbox-' + mid).show();
        }
    });
});

$('[id*="messages-cancel-inbox-"]').click(function() {
    var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $('#messages-verify-inbox-' + mid).hide();
    $('#messages-cancel-inbox-' + mid).hide();
    
    $('#messages-delete-inbox-' + mid).show();
    $('#messages-reply-inbox-' + mid).show();
});

$('[id*="messages-reply-inbox-"]').click(function() {
    var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    window.location.href = '<?php echo SITE_BASE_URL; ?>/user/messages/r' + mid + '/#messages-create';
});

/* Create New / Edit Message */

$('#editor-send').click(function() {
    $('#message-to-msg').hide();
    
    if ($('#message-to').val() == '') {
        $('#message-to-msg').html('You must enter a username.');
        $('#message-to-msg').show();
        return;
    }
    
    if ($('#message-subject').val() == '') {
        $('#message-to-msg').html('You must enter a subject.');
        $('#message-to-msg').show();
        return;
    }
    
    if ($('#editor-text').val() == '') {
        $('#message-to-msg').html('You must enter a message.');
        $('#message-to-msg').show();
        return;
    }
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Message',
               method: 'send',
               params: {
                   username: $('#message-to').val(),
                    subject: $('#message-subject').val(),
                    content: $('#editor-text').val(),
                         id: ($('#message-sent').val() > 0) ? 0 : $('#message-id').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your message has been sent.', { status: 'info' });
            
            setTimeout(function() {
                $('#messages-sent').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/user/messages/#messages-sent';
                if (activeTab && <?php echo (int) empty($_SESSION['objectid']); ?>) { window.location.reload(true); }
            }, 1000);
        } else {    
            $('#message-to-msg').html(data.msg);
            $('#message-to-msg').show();
        }
    });
});

$('#message-to').autocomplete({
    minLength: 2,
    source: function(request, response) {
        $.ajax({
            method: 'POST',
               url: '<?php echo SITE_BASE_URL; ?>/api.php',
              data: {
                  apihash: '<?php echo $_SESSION['apihash']; ?>',
                   object: 'User',
                   method: 'search',
                   params: {
                       term: request.term
                   }
              }
        }).done(function(data) {
            var data = $.parseJSON(data);
            
            if (data.result) {
                response(data.msg);
            } else {
                response([]);
            }
        });
    }
});

$('#editor-save').click(function() {
    $('#message-to-msg').hide();
    
    if ($('#message-to').val() == '') {
        $('#message-to-msg').html('You must enter a username.');
        $('#message-to-msg').show();
        return;
    }
    
    if ($('#message-subject').val() == '') {
        $('#message-to-msg').html('You must enter a subject.');
        $('#message-to-msg').show();
        return;
    }
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Message',
               method: 'save',
               params: {
                   username: $('#message-to').val(),
                    subject: $('#message-subject').val(),
                    content: $('#editor-text').val(),
                         id: ($('#message-sent').val() > 0) ? 0 : $('#message-id').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your message has been saved.', { status: 'info' });
            
            keepAliveClose();
            
            setTimeout(function() {
                $('#messages-drafts').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/user/messages/#messages-drafts';
                if (activeTab && <?php echo (int) empty($_SESSION['objectid']); ?>) { window.location.reload(true); }
            }, 1000);
        } else {    
            $('#message-to-msg').html(data.msg);
            $('#message-to-msg').show();
        }
    });
});

var codemirror = UIkit.htmleditor($('#editor-text'), { markdown: true });
$('#editor-char').text(codemirror.editor.doc.getValue().length);

codemirror.editor.doc.on('change', function(arr) {
    var charLength = codemirror.editor.doc.getValue().length;
    $('#editor-char').text(charLength);
    
    if (charLength > 15000) {
        $('#editor-char').addClass('uk-text-danger');
    } else {
        $('#editor-char').removeClass('uk-text-danger');
    }
    
    if (keepAlive == false) {
        keepAliveStart();
    }
});

/* Saved Drafts */

$('[id*="messages-delete-drafts-"]').click(function() {
    var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $('#messages-delete-drafts-' + mid).hide();
    $('#messages-edit-drafts-' + mid).hide();
    
    $('#messages-verify-drafts-' + mid).show();
    $('#messages-cancel-drafts-' + mid).show();
});

$('[id*="messages-verify-drafts-"]').click(function() {
    var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Message',
               method: 'delete',
               params: { id: mid }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your message has been deleted.', { status: 'info' });
            
            // NOTE: we would have to detect when all the rows in the current page have been deleted
            //       and only then do the page refresh (the timeout below is much easier to implement)
            // $('#messages-row-' + mid).remove();
            
            setTimeout(function() {
                $('#messages-drafts').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/user/messages/#messages-drafts';
                if (activeTab && <?php echo (int) empty($_SESSION['objectid']); ?>) { window.location.reload(true); }
            }, 1000);
        } else {
            $('#messages-verify-drafts-' + mid).hide();
            $('#messages-cancel-drafts-' + mid).hide();
            
            $('#messages-delete-drafts-' + mid).show();
            $('#messages-edit-drafts-' + mid).show();
        }
    });
});

$('[id*="messages-cancel-drafts-"]').click(function() {
    var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $('#messages-verify-drafts-' + mid).hide();
    $('#messages-cancel-drafts-' + mid).hide();
    
    $('#messages-delete-drafts-' + mid).show();
    $('#messages-edit-drafts-' + mid).show();
});

$('[id*="messages-edit-drafts-"]').click(function() {
    var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    window.location.href = '<?php echo SITE_BASE_URL; ?>/user/messages/' + mid + '/#messages-create';
});

/* Paging Bar */

$('[id*="messages-goto-"]').click(function() {
    var tab = this.id.replace('messages-goto-', '');
    tab = tab.substr(0, tab.indexOf('-'));
    
    var pid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $('[id*="messages-page-' + tab + '"]').hide();
    $('#messages-page-' + tab + '-' + pid).show();
    
    $('[id*="messages-goto-' + tab + '"]').removeClass('uk-active');
    $('#messages-goto-' + tab + '-' + pid).addClass('uk-active');
    
    if (pid == 1) {
        $('#messages-prev-' + tab).addClass('uk-disabled');
    } else {
        $('#messages-prev-' + tab).removeClass('uk-disabled');
    }
    
    if (pid == $('[id*="messages-page-' + tab + '"]').length) {
        $('#messages-next-' + tab).addClass('uk-disabled');
    } else {
        $('#messages-next-' + tab).removeClass('uk-disabled');
    }
});

$('[id*="messages-prev-"]').click(function() {
    var tab = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    var activePage = $('#messages-pagination-' + tab + ' .uk-active').first();
    var pid = activePage.attr('id').substr(activePage.attr('id').lastIndexOf('-') + 1);
    var nid = parseInt(pid) - 1;
    
    if (pid == 1) {
        return; // do nothing
    }
    
    $('#messages-page-' + tab + '-' + pid).hide();
    $('#messages-page-' + tab + '-' + nid).show();
    
    $('#messages-goto-' + tab + '-' + pid).removeClass('uk-active');
    $('#messages-goto-' + tab + '-' + nid).addClass('uk-active');
    
    $('#messages-next-' + tab).removeClass('uk-disabled');
    
    if (nid == 1) {
        $('#messages-prev-' + tab).addClass('uk-disabled');
    }
});

$('[id*="messages-next-"]').click(function() {
    var tab = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    var activePage = $('#messages-pagination-' + tab + ' .uk-active').first();
    var pid = activePage.attr('id').substr(activePage.attr('id').lastIndexOf('-') + 1);
    var nid = parseInt(pid) + 1;
    
    var cnt = $('[id*="messages-page-' + tab + '"]').length;
    
    if (pid == cnt) {
        return; // do nothing
    }
    
    $('#messages-page-' + tab + '-' + pid).hide();
    $('#messages-page-' + tab + '-' + nid).show();
    
    $('#messages-goto-' + tab + '-' + pid).removeClass('uk-active');
    $('#messages-goto-' + tab + '-' + nid).addClass('uk-active');
    
    $('#messages-prev-' + tab).removeClass('uk-disabled');
    
    if (nid == cnt) {
        $('#messages-next-' + tab).addClass('uk-disabled');
    }
});

/* Click to View Message */

$('[id*="messages-subject-"]').hover(
    function() {
        var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
        $('#messages-row-' + mid).addClass('messages-row-highlight');
    },
    function() {
        var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
        $('#messages-row-' + mid).removeClass('messages-row-highlight');
    }
);

$('[id*="messages-subject-"]').click(function() {
    var mid = this.id.substr(this.id.lastIndexOf('-') + 1);
    var mtd = $(this);
    
    // determine what tab we are viewing
    var tab = mtd.closest('[id*="messages-page-"]').attr('id');
    tab = tab.replace('messages-page-', '');
    tab = tab.substr(0, tab.indexOf('-'));
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Message',
               method: (tab == 'inbox') ? 'getReply' : 'getById',
               params: { id: mid }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            $('#messages-content-' + mid).html(data.msg.content);
            
            if ($('#messages-icon-' + mid).hasClass('uk-icon-plus-square')) {
                $('#messages-icon-' + mid).removeClass('uk-icon-plus-square');
                $('#messages-icon-' + mid).addClass('uk-icon-minus-square');
            } else {
                $('#messages-icon-' + mid).addClass('uk-icon-plus-square');
                $('#messages-icon-' + mid).removeClass('uk-icon-minus-square');
            }
            
            $('#messages-view-' + mid).toggle();
            
            if (tab == 'inbox') {
                mtd.addClass('uk-text-muted');
            }
        }
    });
});

/* Click to View Announcement */

$('[id*="announcements-subject-"]').hover(
    function() {
        var aid = this.id.substr(this.id.lastIndexOf('-') + 1);
        $('#announcements-row-' + aid).addClass('messages-row-highlight');
    },
    function() {
        var aid = this.id.substr(this.id.lastIndexOf('-') + 1);
        $('#announcements-row-' + aid).removeClass('messages-row-highlight');
    }
);

$('[id*="announcements-subject-"]').click(function() {
    var aid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'rcvdAnnouncement',
               params: { aid: aid }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            $('#announcements-content-' + aid).html(data.msg.content);
            
            if ($('#announcements-icon-' + aid).hasClass('uk-icon-plus-square')) {
                $('#announcements-icon-' + aid).removeClass('uk-icon-plus-square');
                $('#announcements-icon-' + aid).addClass('uk-icon-minus-square');
            } else {
                $('#announcements-icon-' + aid).addClass('uk-icon-plus-square');
                $('#announcements-icon-' + aid).removeClass('uk-icon-minus-square');
            }
            
            $('#announcements-view-' + aid).toggle();
            $('#announcements-subject-' + aid).addClass('uk-text-muted');
        }
    });
});

/* Tab Loading */

if (activeTab) {
    switch (window.location.hash) {
        case '#messages-create' :
            if ('<?php echo substr($_SESSION['objectid'], 0, 1); ?>' == 'u') {
                $.ajax({
                    method: 'POST',
                       url: '<?php echo SITE_BASE_URL; ?>/api.php',
                      data: {
                          apihash: '<?php echo $_SESSION['apihash']; ?>',
                           object: 'User',
                           method: 'getProfile',
                           params: { uid: <?php echo (int) substr($_SESSION['objectid'], 1); ?> }
                      }
                }).done(function(data) {
                    var data = $.parseJSON(data);
                    
                    if (data.result) {
                        $('#message-to').val(data.msg.username);
                    }
                });
            } else if ('<?php echo substr($_SESSION['objectid'], 0, 1); ?>' == 'r') {
                $.ajax({
                    method: 'POST',
                       url: '<?php echo SITE_BASE_URL; ?>/api.php',
                      data: {
                          apihash: '<?php echo $_SESSION['apihash']; ?>',
                           object: 'Message',
                           method: 'getReply',
                           params: { id: <?php echo (int) substr($_SESSION['objectid'], 1); ?> }
                      }
                }).done(function(data) {
                    var data = $.parseJSON(data);
                    
                    if (data.result) {
                        $('#message-id').val(0);
                        $('#message-sent').val(0);
                        $('#message-to').val(data.msg.username);
                        
                        if (data.msg.subject.substr(0, 3) == 'RE:') {
                            var msgSubject = data.msg.subject;
                        } else {
                            var msgSubject = 'RE: ' + data.msg.subject;
                        }
                        
                        $('#message-subject').val(msgSubject);
                        
                        $('#message-replyHTML').html(data.msg.content);
                        $('#message-replyArea').show();
                    }
                });
            } else {
                $.ajax({
                    method: 'POST',
                       url: '<?php echo SITE_BASE_URL; ?>/api.php',
                      data: {
                          apihash: '<?php echo $_SESSION['apihash']; ?>',
                           object: 'Message',
                           method: 'getById',
                           params: { id: <?php echo (int) $_SESSION['objectid']; ?> }
                      }
                }).done(function(data) {
                    var data = $.parseJSON(data);
                    
                    if (data.result) {
                        $('#message-id').val(data.msg.id);
                        $('#message-sent').val(data.msg.sent);
                        $('#message-to').val(data.msg.username);
                        $('#message-subject').val(data.msg.subject);
                        
                        codemirror.editor.doc.setValue(data.msg.contentMD);
                    }
                });
            }
            
            break;
    }
}

$('[data-uk-tab]').on('change.uk.tab', function(event, tab){
    switch(tab.attr('id')) {
        case 'messages-create' :
            setTimeout(function() {
                $('#message-to').focus();
            }, 0);
            break;
    }
});