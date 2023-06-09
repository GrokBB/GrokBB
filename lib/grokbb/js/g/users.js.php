/* G > Users */

if (window.location.hash != '') {
    if (window.location.hash == '#announcements-drafts') {
        activeTab = $('#announcements');
    } else if (window.location.hash != '') {
        activeTab = $(window.location.hash);
    }
    
    if (activeTab) {
        activeTab.addClass('uk-active');
        activeTab.attr('id', '#');
    }
}

/* Users & Moderators */

$('#approved-username, #moderate-username, #banned-username, [id*="badge-username"]').autocomplete({
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

$('#approved-user-add').click(function() {
    $('#approved-user-msg').hide();
    
    if ($('#approved-username').val() == '') {
        $('#approved-user-msg').text('You must enter a username.');
        $('#approved-user-msg').show();
        
        return;
    }
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'addApprovedUser',
               params: {
                        bid: $('#board-id').val(),
                   username: $('#approved-username').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The user has been approved.', { status: 'info' });
            
            $('#approved-user-none').hide();
            $('#approved-user-area').show();
            
            $('#approved-user-table').prepend('<tr id="approved-user-row-' + data.msg + '"><td><a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg + '">' + $('#approved-username').val() + '</a></td><td>Just Now</td><td class="uk-text-center"><button id="approved-user-rem-' + data.msg + '" class="uk-button uk-button-primary">Remove</button></td></tr>');
            
            var newCount = parseInt($('#approved-user-count').text()) + 1;
            $('#approved-user-count').text(newCount);
            
            $('#approved-username').val('');
        } else {
            $('#approved-user-msg').text(data.msg);
            $('#approved-user-msg').show();
        }
    });
});

$('#modal-approved-users').on('click', '[id*="approved-user-rem-"]', function() {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'remApprovedUser',
               params: {
                   bid: $('#board-id').val(),
                   uid: uid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The user is no longer approved.', { status: 'info' });
            
            $('#approved-user-row-' + uid).hide();
            
            var newCount = parseInt($('#approved-user-count').text()) - 1;
            $('#approved-user-count').text(newCount);
        }
    });
});

$('#moderate-user-add').click(function() {
    $('#moderate-user-msg').hide();
    
    if ($('#moderate-username').val() == '') {
        $('#moderate-user-msg').text('You must enter a username.');
        $('#moderate-user-msg').show();
        
        return;
    }
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'addModerator',
               params: {
                        bid: $('#board-id').val(),
                   username: $('#moderate-username').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The user has been added as a moderator.', { status: 'info' });
            
            $('#moderate-user-none').hide();
            $('#moderate-user-area').show();
            
            $('#moderate-user-table').prepend('<tr id="moderate-user-row-' + data.msg + '"><td><a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg + '">' + $('#moderate-username').val() + '</a></td><td>Just Now</td><td class="uk-text-center"><button id="moderate-user-rem-' + data.msg + '" class="uk-button uk-button-primary">Remove</button></td></tr>');
            
            var newCount = parseInt($('#moderate-user-count').text()) + 1;
            $('#moderate-user-count').text(newCount);
            
            $('#moderate-username').val('');
        } else {
            $('#moderate-user-msg').text(data.msg);
            $('#moderate-user-msg').show();
        }
    });
});

$('#modal-moderate-users').on('click', '[id*="moderate-user-rem-"]', function() {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'remModerator',
               params: {
                   bid: $('#board-id').val(),
                   uid: uid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The user is no longer a moderator.', { status: 'info' });
            
            $('#moderate-user-row-' + uid).hide();
            
            var newCount = parseInt($('#moderate-user-count').text()) - 1;
            $('#moderate-user-count').text(newCount);
        }
    });
});

$('#banned-user-add').click(function() {
    $('#banned-user-msg').hide();
    
    if ($('#banned-username').val() == '') {
        $('#banned-user-msg').text('You must enter a username.');
        $('#banned-user-msg').show();
        
        return;
    }
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'addBanned',
               params: {
                        bid: $('#board-id').val(),
                   username: $('#banned-username').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The user has been banned.', { status: 'info' });
            
            $('#banned-user-none').hide();
            $('#banned-user-area').show();
            
            $('#banned-user-table').prepend('<tr id="banned-user-row-' + data.msg + '"><td><a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg + '">' + $('#banned-username').val() + '</a></td><td>Just Now</td><td class="uk-text-center"><button id="banned-user-rem-' + data.msg + '" class="uk-button uk-button-primary">Remove</button></td></tr>');
            
            var newCount = parseInt($('#banned-user-count').text()) + 1;
            $('#banned-user-count').text(newCount);
            
            $('#banned-username').val('');
        } else {
            $('#banned-user-msg').text(data.msg);
            $('#banned-user-msg').show();
        }
    });
});

$('#modal-banned-users').on('click', '[id*="banned-user-rem-"]', function() {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'remBanned',
               params: {
                   bid: $('#board-id').val(),
                   uid: uid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The user is no longer banned.', { status: 'info' });
            
            $('#banned-user-row-' + uid).hide();
            
            var newCount = parseInt($('#banned-user-count').text()) - 1;
            $('#banned-user-count').text(newCount);
        }
    });
});

/* Announcements */

var codeMirror = UIkit.htmleditor($('#announcement-editor'), { markdown: true });
$('#announcement-editor-char').text(codeMirror.editor.doc.getValue().length);

codeMirror.editor.doc.on('change', function(arr) {
    var charLength = codeMirror.editor.doc.getValue().length;
    $('#announcement-editor-char').text(charLength);
    
    if (charLength > 15000) {
        $('#announcement-editor-char').addClass('uk-text-danger');
    } else {
        $('#announcement-editor-char').removeClass('uk-text-danger');
    }
    
    if (keepAlive == false) {
        keepAliveStart();
    }
});

$('#announcement-save').click(function() {
    $('#announcement-msg-div').hide();
    
    if ($('#announcement-subject').val() == '') {
        $('#announcement-msg').html('You must enter a subject.');
        $('#announcement-msg-div').show();
        return;
    }
    
    if ($('#announcement-editor').val() == '') {
        $('#announcement-msg').html('You must enter an announcement.');
        $('#announcement-msg-div').show();
        return;
    }
    
    var aid = $('#announcement-id').val();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'saveAnnouncement',
               params: {
                       bid: $('#board-id').val(),
                   subject: $('#announcement-subject').val(),
                   content: $('#announcement-editor').val(),
                       aid: (aid > 0) ? aid : 0
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('Your announcement has been saved.', { status: 'info' });
            
            setTimeout(function() {
                $('#announcement').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users/#announcements-drafts';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        } else {
            $('#announcement-msg').html(data.msg);
            $('#announcement-msg-div').show();
        }
    });
});

$('[id*="announcement-delete-"]').click(function() {
    var aid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $('#announcement-delete-' + aid).hide();
    $('#announcement-edit-' + aid).hide();
    $('#announcement-send-' + aid).hide();
    
    $('#announcement-verify-' + aid).show();
    $('#announcement-cancel-' + aid).show();
});

$('[id*="announcement-verify-"]').click(function() {
    var aid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'deleteAnnouncement',
               params: {
                   bid: $('#board-id').val(),
                   aid: aid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your announcement has been deleted.', { status: 'info' });
            
            setTimeout(function() {
                $('#announcement').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users/#announcements';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        } else {
            $('#announcement-verify-' + aid).hide();
            $('#announcement-cancel-' + aid).hide();
            
            $('#announcement-delete-' + aid).show();
            $('#announcement-edit-' + aid).show();
            $('#announcement-send-' + aid).show();
        }
    });
});

$('[id*="announcement-cancel-"]').click(function() {
    var aid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $('#announcement-verify-' + aid).hide();
    $('#announcement-cancel-' + aid).hide();
    
    $('#announcement-delete-' + aid).show();
    $('#announcement-edit-' + aid).show();
    $('#announcement-send-' + aid).show();
});

$('[id*="announcement-edit-"]').click(function() {
    var aid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'editAnnouncement',
               params: {
                    bid: $('#board-id').val(),
                    aid: aid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('#announcement-id').val(data.msg.id);
            $('#announcement-subject').val(data.msg.subject);
            codeMirror.editor.doc.setValue(data.msg.content_md);
            
            window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users/#announcements-create';
            
            $('#announcement-save-cancel').show();
        }
    });
});

$('#announcement-save-cancel').click(function() {
    $('#announcement-id').val(0);
    $('#announcement-subject').val('');
    codeMirror.editor.doc.setValue('');
    
    $('#announcement-save-cancel').hide();
});

$('[id*="announcement-send-"]').click(function() {
    var aid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'sendAnnouncement',
               params: {
                    bid: $('#board-id').val(),
                    aid: aid,
                   send: 1
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            UIkit.notify('Your announcement has been sent.', { status: 'info' });
            
            setTimeout(function() {
                $('#announcement').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users/#announcements-drafts';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        }
    });
});

$('[id*="announcement-recall-"]').click(function() {
    var aid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'sendAnnouncement',
               params: {
                    bid: $('#board-id').val(),
                    aid: aid,
                   send: 0
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            UIkit.notify('Your announcement has been recalled.', { status: 'info' });
            
            setTimeout(function() {
                $('#announcement').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users/#announcements-drafts';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        }
    });
});

/* User Badges */

$(function(){
    var progressbar = $('#badge-progressbar');
    var bar = progressbar.find('.uk-progress-bar');
    
    var settings = {
        action: '<?php echo SITE_BASE_URL; ?>/api.php',
        params: {
            apihash: '<?php echo $_SESSION['apihash']; ?>',
             object: 'Board',
             method: 'uploadBadge',
             params: '<?php echo $_SESSION['gbbboard']; ?>,'
        },
        
        filelimit: 1,
        allow: '*.(svg)',
        
        loadstart: function() {
            bar.css('width', '0%').text('0%');
            progressbar.css('display', 'inline-block');
            
            $('#badge-msg').hide();
        },
        
        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css('width', percent + '%').text(percent + '%');
        },

        allcomplete: function(data) {
            var data = $.parseJSON(data);
            
            bar.css('width', '100%').text('100%');
            
            setTimeout(function(){
                progressbar.hide();
                
                if (data.result) {
                    UIkit.notify('Your badge has been added.', { status: 'info' });
                    
                    setTimeout(function() {    
                        $('#badges').attr('id', '#');
                        window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users/#badges';
                        if (activeTab) { window.location.reload(true); }
                    }, 1000);
                } else {
                    $('#badge-desc').focus();
                    
                    $('#badge-msg').html(data.msg);
                    $('#badge-msg').show();
                }
            }, 1250);
        }
    };
    
    $('#badge-select').click(function() {
        $('#badge-msg').hide();
        
        if ($('#badge-desc').val() == '') {
            $('#badge-desc').focus();
            
            $('#badge-msg').html('You must enter a description before you can upload an image.');
            $('#badge-msg').show();
            
            return false;
        }
        
        if ($('#badge-width').val() == '') {
            $('#badge-width').focus();
            
            $('#badge-msg').html('You must enter a width before you can upload an image.');
            $('#badge-msg').show();
            
            return false;
        }
        
        if ($('#badge-height').val() == '') {
            $('#badge-height').focus();
            
            $('#badge-msg').html('You must enter a height before you can upload an image.');
            $('#badge-msg').show();
            
            return false;
        }
        
        settings.params.params += $('#badge-width').val() + ',';
        settings.params.params += $('#badge-height').val() + ',';
        settings.params.params += $('#badge-desc').val();
        UIkit.uploadSelect($(this), settings);
    });
});

var badgeToUpdate = 0;

function updateBadge(id, desc, width, height) {
    badgeToUpdate = id;
    
    $('#badge-update-desc').val(desc);
    $('#badge-update-width').val(width);
    $('#badge-update-height').val(height);
    
    setTimeout(function() {
        $('#badge-update-desc').focus();
    }, 0);
    
    $('#badge-update-msg').hide();
    
    UIkit.modal('#modal-badge-update').show();
}

$('#badge-update-cancel').click(function() {
    badgeToUpdate = 0;
    
    $('#badge-update-desc').val('');
    $('#badge-update-width').val('');
    $('#badge-update-height').val('');
    
    UIkit.modal('#modal-badge-update').hide();
});

$('#badge-update-submit').click(function() {
    $('#badge-update-msg').hide();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'updateBadge',
               params: {
                     name: '<?php echo $_SESSION['gbbboard']; ?>',
                       id: badgeToUpdate,
                    width: $('#badge-update-width').val(),
                   height: $('#badge-update-height').val(),
                     desc: $('#badge-update-desc').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            badgeToUpdate = 0;
        
            $('#badge-update-desc').val('');
            $('#badge-update-width').val('');
            $('#badge-update-height').val('');
            
            UIkit.modal('#modal-badge-update').hide();
            
            UIkit.notify('Your badge has been updated.', { status: 'info' });
            
            setTimeout(function() {
                $('#badges').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users/#badges';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        } else {
            $('#badge-update-desc').focus();
            
            $('#badge-update-msg').html(data.msg);
            $('#badge-update-msg').show();
        }
    });
});

var badgeToDelete = 0;

function deleteBadge(id, desc) {
    badgeToDelete = id;
    
    $('#badge-delete-desc').text(desc);
    UIkit.modal('#modal-badge-delete').show();
}

$('#badge-delete-cancel').click(function() {
    badgeToDelete = 0;
    
    $('#badge-delete-desc').text('');
    UIkit.modal('#modal-badge-delete').hide();
});

$('#badge-delete-confirm').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'deleteBadge',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                     id: badgeToDelete
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        badgeToDelete = 0;
        
        $('#badge-delete-desc').text('');
        UIkit.modal('#modal-badge-delete').hide();
        
        if (data.result) {
            UIkit.notify('Your badge has been deleted.', { status: 'info' });
            
            setTimeout(function() {
                $('#badges').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/users/#badges';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        }
    });
});

$(function(){
    var progressbar = $('#badge-progressbar');
    var bar = progressbar.find('.uk-progress-bar');
    
    var settings = {
        action: '<?php echo SITE_BASE_URL; ?>/api.php',
        params: {
            apihash: '<?php echo $_SESSION['apihash']; ?>',
             object: 'Board',
             method: 'updateBadgeImage',
             params: '<?php echo $_SESSION['gbbboard']; ?>,'
        },
        
        filelimit: 1,
        allow: '*.(svg)',
        
        loadstart: function() {
            bar.css('width', '0%').text('0%');
            progressbar.css('display', 'inline-block');
            
            $('#badge-msg').hide();
        },
        
        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css('width', percent + '%').text(percent + '%');
        },

        allcomplete: function(data) {
            var data = $.parseJSON(data);
            
            bar.css('width', '100%').text('100%');
            
            setTimeout(function(){
                progressbar.hide();
                
                if (data.result) {
                    UIkit.notify('Your badge image has been updated.', { status: 'info' });
                    
                    setTimeout(function() {    
                        $('#settings-categories').attr('id', '#');
                        window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-categories';
                        if (activeTab) { window.location.reload(true); }
                    }, 1000);
                } else {
                    $('#badge-msg').html(data.msg);
                    $('#badge-msg').show();
                }
            }, 1250);
        }
    };
    
    $('[id*="badge-upload-select-"]').click(function() {
        var cid = this.id.substr(this.id.lastIndexOf('-') + 1);
        
        $('#badge-msg').hide();
        
        settings.params.params += cid;
        UIkit.uploadSelect($(this), settings);
    });
});

$('[id*="badge-user-add-"]').click(function() {
    var bad = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#modal-badge-users-id').val(bad);
    
    if ($('#badge-username-' + bad).val() == '') {
        UIkit.notify('You must enter a username.', { status: 'info' });
        return;
    }
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'addBadgeUser',
               params: {
                        bid: $('#board-id').val(),
                        bad: bad,
                   username: $('#badge-username-' + bad).val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The user has been awarded this badge.', { status: 'info' });
            
            var newCount = parseInt($('#badge-user-count-' + bad).text()) + 1;
            $('#badge-user-count-' + bad).text(newCount);
            
            $('#badge-username-' + bad).val('');
        } else {
            UIkit.notify(data.msg, { status: 'info' });
        }
    });
});

$('[id*="badge-users-"]').click(function() {
    var bad = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#modal-badge-users-id').val(bad);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'getBadgeUsers',
               params: {
                        bid: $('#board-id').val(),
                        bad: bad
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            $('#badge-user-desc').html($('#badge-desc-' + bad).val());
            
            $('#badge-user-tbody').empty();
            
            numUsers = data.msg.length;
            
            if (numUsers > 0) {
                for (i = 0; i < numUsers; i++) {
                    $('#badge-user-tbody').append('<tr id="badge-user-row-' + data.msg[i]['userid'] + '"><td><a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg[i]['userid'] + '">' + data.msg[i]['username'] + '</a></td><td>' + data.msg[i]['awarded'] + '</td><td class="uk-text-center"><button id="badge-user-rem-' + data.msg[i]['userid'] + '" class="uk-button uk-button-primary">Remove</button></td></tr>');
                }
                
                $('#badge-user-none').hide();
                $('#badge-user-area').show();
            } else {
                $('#badge-user-none').show();
                $('#badge-user-area').hide();
            }
            
            UIkit.modal('#modal-badge-users').show();
        }
    });
});

$('#modal-badge-users').on('click', '[id*="badge-user-rem-"]', function() {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    var bad = $('#modal-badge-users-id').val();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'remBadgeUser',
               params: {
                   bid: $('#board-id').val(),
                   bad: bad,
                   uid: uid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The user is no longer awarded this badge.', { status: 'info' });
            
            $('#badge-user-row-' + uid).hide();
            
            var newCount = parseInt($('#badge-user-count-' + bad).text()) - 1;
            $('#badge-user-count-' + bad).text(newCount);
        }
    });
});

/* Tab Handling */

$('[data-uk-tab]').on('change.uk.tab', function(event, tab){
    switch(tab.attr('id')) {
        case 'announcements' :
            setTimeout(function() {
                $('#announcement-subject').focus();
            }, 0);
            break;
        case 'badges' :
            setTimeout(function() {
                $('#badge-desc').focus();
            }, 0);
            break;
    }
});