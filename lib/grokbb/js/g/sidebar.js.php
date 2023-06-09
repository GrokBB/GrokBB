/* G > Sidebar */

$('[id*="sidebar-newtopic"]').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/topic/0';
    <?php } ?>
});

$('[id*="sidebar-favorite"]').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    var isFavorite = $('#sidebar-favorite i').hasClass('uk-icon-star');
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: ((isFavorite) ? 'remFavorite' : 'addFavorite'),
               params: {
                   id: '<?php echo $_SESSION['board']->id; ?>'
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            if (isFavorite) {
                $('#sidebar-favorite i').removeClass('uk-icon-star');
                $('#sidebar-favorite i').addClass('uk-icon-star-o');
                
                UIkit.notify('You are no longer subscribed to this board.', { status: 'info' });
            } else {
                $('#sidebar-favorite i').removeClass('uk-icon-star-o');
                $('#sidebar-favorite i').addClass('uk-icon-star');
                
                UIkit.notify('You are now subscribed to this board.', { status: 'info' });
            }
        }
    });
    <?php } ?>
});

/* Modals */

$('[id*="topic-moderate-"]').click(function() {
    var tid = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#modal-moderate-id').val(tid);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'getModerateInfo',
               params: { tid: tid }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            $('#modal-moderate-title').html(data.msg.title);
            
            $('#modal-moderate-user').html('<a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg.userid + '">' + data.msg.username + '</a>');
            $('#modal-moderate-username').val(data.msg.username);
            $('#modal-moderate-userid').val(data.msg.userid);
            
            $('#modal-moderate-sticky').prop('checked', false);
            $('#modal-moderate-private').prop('checked', false);
            $('#modal-moderate-locked').prop('checked', false);
            $('#modal-moderate-approved').prop('checked', false);
            $('#modal-moderate-banned').prop('checked', false);
            
            if (data.msg.sticky > 0) {
                $('#modal-moderate-sticky').prop('checked', true);
            }
            
            if (data.msg.private > 0) {
                $('#modal-moderate-private').prop('checked', true);
            }
            
            if (data.msg.locked > 0) {
                $('#modal-moderate-locked').prop('checked', true);
            }
            
            $('#modal-moderate-notes').val(data.msg.notes);
            
            if (data.msg.approved > 0) {
                $('#modal-moderate-approved').prop('checked', true);
            }
            
            if (data.msg.banned > 0) {
                $('#modal-moderate-banned').prop('checked', true);
            }
            
            pointsCount = data.msg.points;
            
            $('#modal-moderate-points-text').text(pointsCount + ' Point' + ((pointsCount != 1) ? 's' : ''));
            
            if (pointsCount > 0) {
                $('#modal-moderate-points-0').css('color', '#000000');
            
                for (i = 1; i <= pointsCount; i++) {
                    $('#modal-moderate-points-' + i).css('color', '#D85030');
                }
            } else {
                $('#modal-moderate-points-0').css('color', '#D85030');
                $('#modal-moderate-points-1').css('color', '#000000');
                $('#modal-moderate-points-2').css('color', '#000000');
                $('#modal-moderate-points-3').css('color', '#000000');
            }
            
            pointsColor[0] = $('#modal-moderate-points-0').css('color');
            pointsColor[1] = $('#modal-moderate-points-1').css('color');
            pointsColor[2] = $('#modal-moderate-points-2').css('color');
            pointsColor[3] = $('#modal-moderate-points-3').css('color');
            
            var dupeTopicLength = data.msg.duplicateIPs['topic'].length;
            var dupeReplyLength = data.msg.duplicateIPs['reply'].length;
            
            if (dupeTopicLength > 0 || dupeReplyLength > 0) {
                $('#modal-moderate-duplicate-none').hide();
                
                $('#modal-moderate-duplicate-topic-head').hide();
                $('#modal-moderate-duplicate-topic-rows').hide().empty();
                
                $('#modal-moderate-duplicate-reply-head').hide();
                $('#modal-moderate-duplicate-reply-rows').hide().empty();
                
                if (dupeTopicLength > 0) {
                    for (i = 0; i < dupeTopicLength; i++) {
                        $('#modal-moderate-duplicate-topic-rows').append('<div class="uk-width-2-10"><a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg.duplicateIPs['topic'][i]['userid'] + '">' + data.msg.duplicateIPs['topic'][i]['username'] + '</a></div><div class="uk-width-8-10"><a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/'; ?>' + data.msg.duplicateIPs['topic'][i]['topicid'] + '">' + data.msg.duplicateIPs['topic'][i]['topictitle'] + '</a></div>');
                    }
                    
                    $('#modal-moderate-duplicate-topic-head').show();
                    $('#modal-moderate-duplicate-topic-rows').show();
                }
                
                if (dupeReplyLength > 0) {
                    for (i = 0; i < dupeReplyLength; i++) {
                        var replyOpen = data.msg.duplicateIPs['reply'][i]['replyopen'];
                        var replyLink = ((replyOpen > 0) ? '#open' + replyOpen : '#') + 'reply' + data.msg.duplicateIPs['reply'][i]['replyid'];
                        $('#modal-moderate-duplicate-reply-rows').append('<div class="uk-width-2-10"><a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg.duplicateIPs['reply'][i]['userid'] + '">' + data.msg.duplicateIPs['reply'][i]['username'] + '</a></div><div class="uk-width-8-10"><a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/'; ?>' + data.msg.duplicateIPs['reply'][i]['topicid'] + replyLink + '">' + data.msg.duplicateIPs['reply'][i]['topictitle'] + '</a></div>');
                    }
                    
                    $('#modal-moderate-duplicate-reply-head').show();
                    $('#modal-moderate-duplicate-reply-rows').show();
                }
                
                $('#modal-moderate-duplicate-warn').show();
            } else {
                $('#modal-moderate-duplicate-warn').hide();
                $('#modal-moderate-duplicate-none').show();
            }
            
            UIkit.modal('#modal-moderate').show();
        }
    });
});

$('#modal-moderate-duplicate-warn-toggle').click(function() {
    var toggleIcon = $('#modal-moderate-duplicate-warn-icon');
    
    if (toggleIcon.hasClass('uk-icon-caret-square-o-up')) {
        toggleIcon.removeClass('uk-icon-caret-square-o-up');
        toggleIcon.addClass('uk-icon-caret-square-o-down');
        
        $(this).attr('title', 'Click to Hide Details');
        
        $('#modal-moderate-duplicate-warn-more').show();
    } else {
        toggleIcon.removeClass('uk-icon-caret-square-o-down');
        toggleIcon.addClass('uk-icon-caret-square-o-up');
        
        $(this).attr('title', 'Click to View Details');
        
        $('#modal-moderate-duplicate-warn-more').hide();
    }
});

$('#modal-moderate-reload').click(function() {
    UIkit.modal('#modal-moderate').hide();
    window.location.reload();
});

$('#modal-moderate-sticky').click(function() {
    var tid = $('#modal-moderate-id').val();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'setSticky',
               params: {
                      tid: tid,
                   sticky: ($('#modal-moderate-sticky').prop('checked')) ? 1 : 0
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            if ($('#modal-moderate-sticky').prop('checked')) {
                UIkit.notify('The topic is now sticky.', { status: 'info' });
            } else {
                UIkit.notify('The topic is no longer sticky.', { status: 'info' });
            }
        }
    });
});

$('#modal-moderate-private').click(function() {
    var tid = $('#modal-moderate-id').val();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'setPrivate',
               params: {
                       tid: tid,
                   private: ($('#modal-moderate-private').prop('checked')) ? 1 : 0
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            if ($('#modal-moderate-private').prop('checked')) {
                UIkit.notify('The topic is now private.', { status: 'info' });
            } else {
                UIkit.notify('The topic is no longer private.', { status: 'info' });
            }
        }
    });
});

$('#modal-moderate-locked').click(function() {
    var tid = $('#modal-moderate-id').val();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'setLocked',
               params: {
                      tid: tid,
                   sticky: ($('#modal-moderate-locked').prop('checked')) ? 1 : 0
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            if ($('#modal-moderate-locked').prop('checked')) {
                UIkit.notify('The topic is now locked.', { status: 'info' });
            } else {
                UIkit.notify('The topic is no longer locked.', { status: 'info' });
            }
        }
    });
});

$('#modal-moderate-notes-update').click(function() {
    var tid = $('#modal-moderate-id').val();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'setNotes',
               params: {
                     tid: tid,
                   notes: $('#modal-moderate-notes').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The topic\'s notes have been updated.', { status: 'info' });
        }
    });
});

$('#modal-moderate-banned').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: ($('#modal-moderate-banned').prop('checked')) ? 'addBanned' : 'remBanned',
               params: {
                        bid: '<?php echo $_SESSION['board']->id; ?>',
                   username: ($('#modal-moderate-banned').prop('checked')) ? $('#modal-moderate-username').val() : $('#modal-moderate-userid').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            if ($('#modal-moderate-banned').prop('checked')) {
                UIkit.notify('The user is now banned.', { status: 'info' });
            } else {
                UIkit.notify('The user is no longer banned.', { status: 'info' });
            }
        } else {
            UIkit.notify(data.msg, { status: 'info' });
        }
    });
});

$('#modal-moderate-approved').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: ($('#modal-moderate-approved').prop('checked')) ? 'addApprovedUser' : 'remApprovedUser',
               params: {
                        bid: '<?php echo $_SESSION['board']->id; ?>',
                   username: ($('#modal-moderate-approved').prop('checked')) ? $('#modal-moderate-username').val() : $('#modal-moderate-userid').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            if ($('#modal-moderate-approved').prop('checked')) {
                UIkit.notify('The user is now approved.', { status: 'info' });
            } else {
                UIkit.notify('The user is no longer approved.', { status: 'info' });
            }
        } else {
            UIkit.notify(data.msg, { status: 'info' });
        }
    });
});

var pointsCount = 0;
var pointsColor = {};

$('[id*="modal-moderate-points-"]').hover(
    function() {
        var num = this.id.substr(this.id.lastIndexOf('-') + 1);
        if (num == 'text') { return; }
        
        if (num > 0) {
            $('#modal-moderate-points-0').css('color', '#000000');
        
            for (i = 1; i <= 3; i++) {
                if (i <= num) {
                    $('#modal-moderate-points-' + i).css('color', '#D85030');
                } else {
                    $('#modal-moderate-points-' + i).css('color', '#000000');
                }
            }
        } else {
            $('#modal-moderate-points-0').css('color', '#D85030');
            $('#modal-moderate-points-1').css('color', '#000000');
            $('#modal-moderate-points-2').css('color', '#000000');
            $('#modal-moderate-points-3').css('color', '#000000');
        }
        
        $('#modal-moderate-points-text').text(num + ' Point' + ((num != 1) ? 's' : ''));
    },
    function() {
        $('#modal-moderate-points-0').css('color', pointsColor[0]);
        $('#modal-moderate-points-1').css('color', pointsColor[1]);
        $('#modal-moderate-points-2').css('color', pointsColor[2]);
        $('#modal-moderate-points-3').css('color', pointsColor[3]);
        
        $('#modal-moderate-points-text').text(pointsCount + ' Point' + ((pointsCount != 1) ? 's' : ''));
    }
);

$('[id*="modal-moderate-points-"]').click(function() {
    var num = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'addModeratorPoints',
               params: {
                      tid: $('#modal-moderate-id').val(),
                      uid: $('#modal-moderate-userid').val(),
                   points: num
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            pointsColor[0] = $('#modal-moderate-points-0').css('color');
            pointsColor[1] = $('#modal-moderate-points-1').css('color');
            pointsColor[2] = $('#modal-moderate-points-2').css('color');
            pointsColor[3] = $('#modal-moderate-points-3').css('color');
            
            pointsCount = num;
            
            UIkit.notify('The user\'s moderator points have been updated.', { status: 'info' });
        }
    });
});

$('[id*="topic-report-"]').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    var tid = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#modal-report-id').val(tid);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'getTitle',
               params: { tid: tid }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            $('#modal-report-title').html(data.msg);
            
            UIkit.modal('#modal-report').show();
        }
    });
    <?php } ?>
});

var codeMirrorReport = UIkit.htmleditor($('#editor-report'), { markdown: true, height: 200 });
$('#editor-report-char').text(codeMirrorReport.editor.doc.getValue().length);

codeMirrorReport.editor.doc.on('change', function(arr) {
    var charLength = codeMirrorReport.editor.doc.getValue().length;
    $('#editor-report-char').text(charLength);
    
    if (charLength > 15000) {
        $('#editor-report-char').addClass('uk-text-danger');
    } else {
        $('#editor-report-char').removeClass('uk-text-danger');
    }
    
    if (keepAlive == false) {
        keepAliveStart();
    }
});

$('#modal-report-send').click(function() {
    var tid = $('#modal-report-id').val();
    
    $('#modal-report-msg').hide();
    
    if ($('#editor-report').val() == '') {
        $('#modal-report-msg').html('You must enter a message.');
        $('#modal-report-msg').show();
        
        return;
    }
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'reportTopic',
               params: {
                       bid: '<?php echo $_SESSION['board']->id; ?>',
                       tid: tid,
                   message: $('#editor-report').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('Your report has been sent to the moderators.', { status: 'info' });
            
            keepAliveClose();
            
            UIkit.modal('#modal-report').hide();
        } else {
            $('#modal-report-msg').html(data.msg);
            $('#modal-report-msg').show();
        }
    });
});

function displayBadges(uid) {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'User',
               method: 'getBadges',
               params: {
                   uid: uid,
                   bid: <?php echo $_SESSION['board']->id; ?>
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            numBadges = data.msg.length;
            
            if (numBadges > 0) {
                $('#modal-badges-count').html('<span class="uk-badge uk-badge-success" style="position: relative; bottom: 1px;">' + numBadges + '</span> badge' + ((numBadges > 1) ? 's' : ''));
                $('#modal-badges-username').html('<a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + uid + '">' + $('#user-username-' + uid).text() + '</a>');
                
                $('#modal-badges-display').empty();
                
                for (i = 0; i < numBadges; i++) {
                    $('#modal-badges-display').append('<div class="uk-width-2-10" style="margin-right: 10px; width: ' + data.msg[i]['width'] + 'px"><object data="<?php echo SITE_BASE_URL . '/img.php?bid=' . $_SESSION['board']->id . '&bad='; ?>' + data.msg[i]['id'] + '" type="image/svg+xml" width="' + data.msg[i]['width'] + '" height="' + data.msg[i]['height'] + '" data-uk-tooltip="{ pos: \'bottom\' }" title="' + data.msg[i]['desc'] + '"></object></div>');
                }
                
                UIkit.modal('#modal-badges').show();
            }
        }
    });
}