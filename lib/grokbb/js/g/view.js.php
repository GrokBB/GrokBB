/* G > View */

$('#topic-view').on('click', '[id*="topic-user-"], [id*="reply-user-"]', function(e) {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    if ($(e.target).hasClass('uk-icon-shield')) {
        displayBadges(uid);
    } else {
        window.location.href = '<?php echo SITE_BASE_URL; ?>/user/view/' + uid;
    }
});

$('#topic-save').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'saveForLater',
               params: {
                    tid: <?php echo (int) $_SESSION['objectid']; ?>
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('#topic-save').hide();
            $('#topic-saved').show();
        }
    });
    <?php } ?>
});

$('#topic-unsave').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'unsaveForLater',
               params: {
                    tid: <?php echo (int) $_SESSION['objectid']; ?>
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('#topic-save').show();
            $('#topic-saved').hide();
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
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            return true;
        } else {
            UIkit.notify('This tag was added by a moderator.<br />It can not be deleted by you.', { status: 'info' });
            
            return false;
        }
    });
}

var tagHandlerSettings = {
    assignedTags: tagsByTopic[<?php echo (int) $_SESSION['objectid']; ?>],
           onAdd: function(tag) { return addTag(<?php echo (int) $_SESSION['objectid']; ?>, tag); },
        onDelete: function(tag) { return delTag(<?php echo (int) $_SESSION['objectid']; ?>, tag); }
}

jQuery.extend(tagHandlerSettings, tagHandlerDefaults);

$('#topic-tags').tagHandler(tagHandlerSettings);

$('#topic-title').click(function() {
    $('#topic-title').hide();
    
    $('#topic-title-edit').show();
    $('#topic-title-edit').focus();
});

$('#topic-title-edit').keyup(function(e) {
    // ENTER
    if (e.which == 13) {
        $.ajax({
            method: 'POST',
               url: '<?php echo SITE_BASE_URL; ?>/api.php',
              data: {
                  apihash: '<?php echo $_SESSION['apihash']; ?>',
                   object: 'Topic',
                   method: 'updateTitle',
                   params: {
                         tid: <?php echo (int) $_SESSION['objectid']; ?>,
                       title: $('#topic-title-edit').val()
                   }
              }
        }).done(function(data) {
            var data = $.parseJSON(data);
                
            if (data.result) {
                UIkit.notify('Your topic\'s title has been updated.', { status: 'info' });
                
                $('#topic-title').html(data.msg + '&nbsp;');
                    
                $('#topic-title-edit').hide();
                $('#topic-title').show();
            }
        });
    // ESC
    } else if (e.which == 27) {
        $('#topic-title-edit').hide();
        $('#topic-title').show();
    }
});

var codeMirrorTopic = false;

$('#topic-edit').click(function() {
    if (codeMirrorTopic == false) {
        codeMirrorTopic = UIkit.htmleditor($('#topic-editor'), { markdown: true });
        $('#topic-editor-char').text(codeMirrorTopic.editor.doc.getValue().length);
        
        codeMirrorTopic.editor.doc.on('change', function(arr) {
            var charLength = codeMirrorTopic.editor.doc.getValue().length;
            $('#topic-editor-char').text(charLength);
            
            if (charLength > 15000) {
                $('#topic-editor-char').addClass('uk-text-danger');
            } else {
                $('#topic-editor-char').removeClass('uk-text-danger');
            }
            
            if (keepAlive == false) {
                keepAliveStart();
            }
        });
    }
    
    $('#topic-content').hide();
    $('#topic-content-buttons').hide();
    $('#topic-media').hide();
    
    $('#topic-content-edit').show();
    $('#topic-media-edit').show();
});

$('#topic-edit-cancel').click(function() {
    $('#topic-content-edit').hide();
    $('#topic-media-edit').hide();
    
    $('#topic-content').show();
    $('#topic-content-buttons').show();
    $('#topic-media').show();
});

$('#topic-edit-update').click(function() {
    media = [];
    
    $('.topic-media-div').each(function() {
        url = $(this).find('.topic-media-url').first();
        url = url.val();
        
        if (url != '') {
            data = {};
            data.url = url;
            
            txt = $(this).find('.topic-media-txt').first();
            txt = txt.val();
            
            if (txt != '') {
                data.txt = txt;
            }
            
            media.push(data);
        }
    });
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'updateContent',
               params: {
                       tid: <?php echo (int) $_SESSION['objectid']; ?>,
                   content: $('#topic-editor').val(),
                     media: media
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your topic has been updated.', { status: 'info' });
            
            $('#topic-content').html(data.msg);
            
            keepAliveClose();
            
            if (media.length > 0) {
                setTimeout(function() {
                    window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/<?php echo (int) $_SESSION['objectid']; ?>';
                    window.location.reload(true);
                }, 1000);
            } else {
                $('#topic-content-edit').hide();
                $('#topic-media-edit').hide();
                
                $('#topic-content').show();
                $('#topic-content-updated').html('Just Now');
                $('#topic-content-updated-by').html('<a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $_SESSION['user']->id; ?>"><?php echo $_SESSION['user']->username; ?></a>');
                $('#topic-content-buttons').show();
            }
        }
    });
});

$('.topic-media-add').click(function() {
    $('#topic-media-all').append('<div class="uk-width-8-10 gbb-spacing topic-media-div"><input class="uk-width-1-1 topic-media-url" type="text" maxlength="2000" value=""><div class="uk-margin-top uk-margin-bottom"><strong>Caption</strong>&nbsp;&nbsp;<input class="uk-width-1-2 topic-media-txt" type="text" maxlength="120" value=""></div></div>');
});

$('#topic-delete').click(function() {
    $('#topic-delete').hide();
    $('#topic-edit').hide();
    
    $('#topic-delete-confirm').show();
    $('#topic-delete-cancel').show();
});

$('#topic-delete-cancel').click(function() {
    $('#topic-delete-confirm').hide();
    $('#topic-delete-cancel').hide();
    
    $('#topic-delete').show();
    $('#topic-edit').show();
});

$('#topic-delete-confirm').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'delete',
               params: {
                       tid: <?php echo (int) $_SESSION['objectid']; ?>,
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your topic has been deleted.', { status: 'info' });
            
            setTimeout(function() {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/<?php echo (int) $_SESSION['objectid']; ?>';
                window.location.reload(true);
            }, 1000);
            
            /*
            $('#topic-delete-confirm').hide();
            $('#topic-delete-cancel').hide();
            
            $('#topic-restore').show();
            $('#topic-edit').show();
            */
        }
    });
});

$('#topic-restore').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'restore',
               params: {
                       tid: <?php echo (int) $_SESSION['objectid']; ?>,
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your topic has been restored.', { status: 'info' });
            
            setTimeout(function() {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/<?php echo (int) $_SESSION['objectid']; ?>';
                window.location.reload(true);
            }, 1000);
            
            /*
            $('#topic-restore').hide();
            $('#topic-delete').show();
            $('#topic-edit').show();
            */
        }
    });
});

var codeMirrorTopicReply = false;

$('#topic-reply-create').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    
    if (codeMirrorTopicReply == false) {
        codeMirrorTopicReply = UIkit.htmleditor($('#topic-reply-editor'), { markdown: true, height: '250px' });
        $('#topic-reply-editor-char').text(codeMirrorTopicReply.editor.doc.getValue().length);
        
        codeMirrorTopicReply.editor.doc.on('change', function(arr) {
            var charLength = codeMirrorTopicReply.editor.doc.getValue().length;
            $('#topic-reply-editor-char').text(charLength);
            
            if (charLength > 15000) {
                $('#topic-reply-editor-char').addClass('uk-text-danger');
            } else {
                $('#topic-reply-editor-char').removeClass('uk-text-danger');
            }
            
            if (keepAlive == false) {
                keepAliveStart();
            }
        });
    }
    
    $('#topic-reply-div').show();
    <?php } ?>
});

$('#topic-reply-cancel').click(function() {
    $('#topic-reply-div').hide();
});

$('#topic-reply-insert').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'create',
               params: {
                       tid: '<?php echo (int) $_SESSION['objectid']; ?>',
                       pid: 0,
                   content: $('#topic-reply-editor').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your reply has been added.', { status: 'info' });
            
            keepAliveClose();
            
            setTimeout(function() {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/<?php echo (int) $_SESSION['objectid']; ?>#reply' + data.msg;
                window.location.reload(true);
            }, 1000);
        }
    });
});

/* Replies */

$('[id*="reply-sort-"]').click(function() {
    var sort = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/search/rply/'; ?>' + sort
    }).done(function(data) {
        window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/<?php echo (int) $_SESSION['objectid']; ?>#sort';
        window.location.reload(true);
    });
});

var codeMirrorReply = false;

$('[id*="reply-content-buttons"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-edit-"]', function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'getContentMD',
               params: {
                    rid: rid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('#reply-editor-' + rid).val(data.msg);
            
            if (codeMirrorReply == false) {
                codeMirrorReply = UIkit.htmleditor($('#reply-editor-' + rid), { markdown: true, height: '250px', maxsplitsize: '800px' });
                $('#reply-editor-char-' + rid).text(codeMirrorReply.editor.doc.getValue().length);
                
                codeMirrorReply.editor.doc.on('change', function(arr) {
                    var charLength = codeMirrorReply.editor.doc.getValue().length;
                    $('#reply-editor-char-' + rid).text(charLength);
                    
                    if (charLength > 15000) {
                        $('#reply-editor-char-' + rid).addClass('uk-text-danger');
                    } else {
                        $('#reply-editor-char-' + rid).removeClass('uk-text-danger');
                    }
                    
                    if (keepAlive == false) {
                        keepAliveStart();
                    }
                });
            } else {
                codeMirrorReply.editor.doc.setValue(data.msg);
            }
            
            setTimeout(function() {
                $(codeMirrorReply.editor.display.input.getField()).focus();
                codeMirrorReply.editor.refresh();
            }, 250);
            
            $('#reply-content-' + rid).hide();
            $('#reply-content-buttons-' + rid).hide();
            
            $('#reply-content-edit-' + rid).show();
        }
    });
    <?php } ?>
});

$('[id*="reply-content-edit"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-content-cancel-"]', function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $('#reply-content-edit-' + rid).hide();
    
    $('#reply-content-' + rid).show();
    $('#reply-content-buttons-' + rid).show();
});

$('[id*="reply-content-edit"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-content-update-"]', function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'updateContent',
               params: {
                       rid: rid,
                   content: $('#reply-editor-' + rid).val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your reply has been updated.', { status: 'info' });
            
            $('#reply-content-' + rid).html(data.msg);
                
            $('#reply-content-edit-' + rid).hide();
            
            $('#reply-content-' + rid).show();
            $('#reply-content-updated-' + rid).html('Just Now');
            <?php if (isset($_SESSION['user'])) { ?>
            $('#reply-content-updated-by-' + rid).html('<a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $_SESSION['user']->id; ?>"><?php echo $_SESSION['user']->username; ?></a>');
            <?php } else { ?>
            $('#reply-content-updated-by-' + rid).html('');
            <?php } ?>
            $('#reply-content-buttons-' + rid).show();
            
            keepAliveClose();
        }
    });
});

$('[id*="reply-content-buttons"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-save-"]', function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'saveForLater',
               params: {
                    rid: rid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('#reply-save-' + rid).hide();
            $('#reply-unsave-' + rid).show();
        }
    });
    <?php } ?>
});

$('[id*="reply-content-buttons"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-unsave-"]', function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'unsaveForLater',
               params: {
                    rid: rid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('#reply-save-' + rid).show();
            $('#reply-unsave-' + rid).hide();
        }
    });
    <?php } ?>
});

$('[id*="reply-content-buttons"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-delete-"]', function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    <?php if (isset($_SESSION['user']) && $_SESSION['user']->isModerator) { ?>
    $('#reply-moderate-' + rid).hide();
    $('#reply-save-' + rid).hide();
    <?php } ?>
    $('#reply-delete-' + rid).hide();
    $('#reply-edit-' + rid).hide();
    $('#reply-reply-create-' + rid).hide();
    
    $('#reply-delete-confirm-' + rid).show();
    $('#reply-delete-cancel-' + rid).show();
});

$('[id*="reply-content-buttons"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-delete-cancel-"]', function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $('#reply-delete-confirm-' + rid).hide();
    $('#reply-delete-cancel-' + rid).hide();
    
    <?php if (isset($_SESSION['user']) && $_SESSION['user']->isModerator == $_SESSION['board']->id) { ?>
    $('#reply-moderate-' + rid).show();
    $('#reply-save-' + rid).show();
    <?php } ?>
    $('#reply-delete-' + rid).show();
    $('#reply-edit-' + rid).show();
    $('#reply-reply-create-' + rid).show();
});

$('[id*="reply-content-buttons"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-delete-confirm-"]', function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'delete',
               params: {
                       rid: rid,
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your reply has been deleted.', { status: 'info' });
            
            $('#reply-content-' + rid).html('<div class="uk-alert uk-alert-danger" data-uk-alert>This reply has been deleted.</div>');
            
            $('#reply-delete-confirm-' + rid).hide();
            $('#reply-delete-cancel-' + rid).hide();
            
            <?php if (isset($_SESSION['user']) && $_SESSION['user']->isModerator == $_SESSION['board']->id) { ?>
            $('#reply-moderate-' + rid).show();
            $('#reply-save-' + rid).show();
            <?php } ?>
            $('#reply-restore-' + rid).show();
            $('#reply-edit-' + rid).show();
            $('#reply-reply-create-' + rid).show();
        }
    });
});

$('[id*="reply-content-buttons"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-restore-"]', function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'restore',
               params: {
                       rid: rid,
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your reply has been restored.', { status: 'info' });
            
            $('#reply-content-' + rid).html(data.msg);
            
            $('#reply-restore-' + rid).hide();
            
            <?php if (isset($_SESSION['user']) && $_SESSION['user']->isModerator == $_SESSION['board']->id) { ?>
            $('#reply-moderate-' + rid).show();
            $('#reply-save-' + rid).show();
            <?php } ?>
            
            $('#reply-delete-' + rid).show();
            $('#reply-edit-' + rid).show();
            $('#reply-reply-create' + rid).show();
        }
    });
});

var codeMirrorReplyReply = false;

$('[id*="reply-reply-create-"]').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    if (codeMirrorReplyReply == false) {
        codeMirrorReplyReply = UIkit.htmleditor($('#reply-reply-editor-' + rid), { markdown: true, height: '250px' });
        $('#reply-reply-editor-char-' + rid).text(codeMirrorReplyReply.editor.doc.getValue().length);
        
        codeMirrorReplyReply.editor.doc.on('change', function(arr) {
            var charLength = codeMirrorReplyReply.editor.doc.getValue().length;
            $('#reply-reply-editor-char-' + rid).text(charLength);
            
            if (charLength > 15000) {
                $('#reply-reply-editor-char-' + rid).addClass('uk-text-danger');
            } else {
                $('#reply-reply-editor-char-' + rid).removeClass('uk-text-danger');
            }
            
            if (keepAlive == false) {
                keepAliveStart();
            }
        });
    }
    
    $('#reply-reply-div-' + rid).show();
    <?php } ?>
});

$('[id*="reply-reply-cancel-"]').click(function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#reply-reply-div-' + rid).hide();
});

$('[id*="reply-reply-insert-"]').click(function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'create',
               params: {
                       tid: '<?php echo (int) $_SESSION['objectid']; ?>',
                       rid: rid,
                   content: $('#reply-reply-editor-' + rid).val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your reply has been added.', { status: 'info' });
            
            keepAliveClose();
            
            setTimeout(function() {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/<?php echo (int) $_SESSION['objectid']; ?>#open' + rid + 'reply' + data.msg;
                window.location.reload(true);
            }, 1000);
        }
    });
});

$('[id*="reply-responses-show-"]').click(function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#reply-responses-div-' + rid).show();
    
    $.ajax({
        method: 'GET',
           url: '<?php echo SITE_BASE_URL; ?>/app/g/view-ajax.php',
          data: {
             tid: '<?php echo (int) $_SESSION['objectid']; ?>',
             rid: rid
          }
    }).done(function(data) {
        if (data != '') {
            $('#reply-responses-div-' + rid).html(data);
            
            $('#reply-responses-show-count-' + rid).text($('#reply-responses-count-' + rid).val());
            $('#reply-responses-hide-count-' + rid).text($('#reply-responses-count-' + rid).val());
            
            if (goTo > 0) {
                window.location.href = '#reply' + goTo;
                goTo = false;
            }
        }
    });
    
    $('#reply-responses-show-' + rid).hide();
    $('#reply-responses-hide-' + rid).show();
});

$('[id*="reply-content-buttons"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-responses-hide-"]', function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#reply-responses-div-' + rid).hide();
    
    $('#reply-responses-hide-' + rid).hide();
    $('#reply-responses-show-' + rid).show();
});

var codeMirrorReplyQuote = false;

$('[id*="reply-responses-div-"]').on('click', '[id*="reply-quote-create-"]', function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    if (codeMirrorReplyQuote == false) {
        codeMirrorReplyQuote = UIkit.htmleditor($('#reply-quote-editor-' + rid), { markdown: true, height: '250px' });
        $('#reply-quote-editor-char-' + rid).text(codeMirrorReplyQuote.editor.doc.getValue().length);
        
        codeMirrorReplyQuote.editor.doc.on('change', function(arr) {
            var charLength = codeMirrorReplyQuote.editor.doc.getValue().length;
            $('#reply-quote-editor-char-' + rid).text(charLength);
            
            if (charLength > 15000) {
                $('#reply-quote-editor-char-' + rid).addClass('uk-text-danger');
            } else {
                $('#reply-quote-editor-char-' + rid).removeClass('uk-text-danger');
            }
            
            if (keepAlive == false) {
                keepAliveStart();
            }
        });
    }
    
    $('#reply-quote-div-' + rid).show();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'getContentMD',
               params: {
                       rid: rid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            var username = $('.user-username-' + rid).text();
            var openLink = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/<?php echo (int) $_SESSION['objectid']; ?>#reply' + rid;
            codeMirrorReplyQuote.editor.doc.setValue('> [' + username + '](' + openLink + ') wrote ...\n' + data.msg.replace(/\n/g, '\n> ').replace(/> \[/g, '> > \[') + '\n\n');
            
            setTimeout(function() {
                $(codeMirrorReplyQuote.editor.display.input.getField()).focus();
                codeMirrorReplyQuote.editor.setCursor(codeMirrorReplyQuote.editor.lineCount(), 0);
                codeMirrorReplyQuote.editor.refresh();
            }, 250);
        }
    });
    
    // automatic topic splitting has been disabled
    // $('#modal-reply-create').attr('href', '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/topic/0-' + rid);
    // UIkit.modal('#modal-reply').show();
    <?php } ?>
});

$('[id*="reply-responses-div-"]').on('click', '[id*="reply-quote-cancel-"]', function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#reply-quote-div-' + rid).hide();
});

$('[id*="reply-responses-div-"]').on('click', '[id*="reply-quote-insert-"]', function() {
    var qid = this.id.substr(this.id.lastIndexOf('-') + 1).split('r');
    var pid = qid[0];
    var rid = qid[1];
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'create',
               params: {
                       tid: '<?php echo (int) $_SESSION['objectid']; ?>',
                       pid: pid,
                   content: $('#reply-quote-editor-' + rid).val(),
                       qid: rid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your reply has been added.', { status: 'info' });
            
            keepAliveClose();
            
            setTimeout(function() {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/<?php echo (int) $_SESSION['objectid']; ?>#open' + pid + 'reply' + data.msg;
                window.location.reload(true);
            }, 1000);
        }
    });
});

/* Modals */

$('[id*="change-category-"]').click(function() {
    var cid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Topic',
               method: 'updateCategory',
               params: {
                   tid: <?php echo (int) $_SESSION['objectid']; ?>,
                   cid: cid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your topic\'s category has been updated.', { status: 'info' });
            
            setTimeout(function() {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/<?php echo (int) $_SESSION['objectid']; ?>';
            }, 1000);
        }
    });
});

$('[id*="reply-content-buttons"], [id*="reply-responses-div-"]')
.on('click', '[id*="reply-moderate-"]', function() {
    var rid = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#modal-moderate-reply-id').val(rid);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'getModerateInfo',
               params: { rid: rid }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            $('#modal-moderate-reply-user').html('<a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg.userid + '">' + data.msg.username + '</a>');
            $('#modal-moderate-reply-username').val(data.msg.username);
            $('#modal-moderate-reply-userid').val(data.msg.userid);
            
            $('#modal-moderate-reply-approved').prop('checked', false);
            $('#modal-moderate-reply-banned').prop('checked', false);
            
            if (data.msg.approved > 0) {
                $('#modal-moderate-reply-approved').prop('checked', true);
            }
            
            if (data.msg.banned > 0) {
                $('#modal-moderate-reply-banned').prop('checked', true);
            }
            
            pointsCountReply = data.msg.points;
            
            $('#modal-moderate-reply-points-text').text(pointsCountReply + ' Point' + ((pointsCountReply != 1) ? 's' : ''));
            
            if (pointsCountReply > 0) {
                $('#modal-moderate-reply-points-0').css('color', '#000000');
            
                for (i = 1; i <= pointsCountReply; i++) {
                    $('#modal-moderate-reply-points-' + i).css('color', '#D85030');
                }
            } else {
                $('#modal-moderate-reply-points-0').css('color', '#D85030');
                $('#modal-moderate-reply-points-1').css('color', '#000000');
                $('#modal-moderate-reply-points-2').css('color', '#000000');
                $('#modal-moderate-reply-points-3').css('color', '#000000');
            }
            
            pointsColorReply[0] = $('#modal-moderate-reply-points-0').css('color');
            pointsColorReply[1] = $('#modal-moderate-reply-points-1').css('color');
            pointsColorReply[2] = $('#modal-moderate-reply-points-2').css('color');
            pointsColorReply[3] = $('#modal-moderate-reply-points-3').css('color');
            
            var dupeTopicLength = data.msg.duplicateIPs['topic'].length;
            var dupeReplyLength = data.msg.duplicateIPs['reply'].length;
            
            if (dupeTopicLength > 0 || dupeReplyLength > 0) {
                $('#modal-moderate-reply-duplicate-none').hide();
                
                $('#modal-moderate-reply-duplicate-topic-head').hide();
                $('#modal-moderate-reply-duplicate-topic-rows').hide().empty();
                
                $('#modal-moderate-reply-duplicate-reply-head').hide();
                $('#modal-moderate-reply-duplicate-reply-rows').hide().empty();
                
                if (dupeTopicLength > 0) {
                    for (i = 0; i < dupeTopicLength; i++) {
                        $('#modal-moderate-reply-duplicate-topic-rows').append('<div class="uk-width-2-10"><a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg.duplicateIPs['topic'][i]['userid'] + '">' + data.msg.duplicateIPs['topic'][i]['username'] + '</a></div><div class="uk-width-8-10"><a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/'; ?>' + data.msg.duplicateIPs['topic'][i]['topicid'] + '">' + data.msg.duplicateIPs['topic'][i]['topictitle'] + '</a></div>');
                    }
                    
                    $('#modal-moderate-reply-duplicate-topic-head').show();
                    $('#modal-moderate-reply-duplicate-topic-rows').show();
                }
                
                if (dupeReplyLength > 0) {
                    for (i = 0; i < dupeReplyLength; i++) {
                        var replyOpen = data.msg.duplicateIPs['reply'][i]['replyopen'];
                        var replyLink = ((replyOpen > 0) ? '#open' + replyOpen : '#') + 'reply' + data.msg.duplicateIPs['reply'][i]['replyid'];
                        $('#modal-moderate-reply-duplicate-reply-rows').append('<div class="uk-width-2-10"><a href="<?php echo SITE_BASE_URL . '/user/view/'; ?>' + data.msg.duplicateIPs['reply'][i]['userid'] + '">' + data.msg.duplicateIPs['reply'][i]['username'] + '</a></div><div class="uk-width-8-10"><a href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/'; ?>' + data.msg.duplicateIPs['reply'][i]['topicid'] + replyLink + '">' + data.msg.duplicateIPs['reply'][i]['topictitle'] + '</a></div>');
                    }
                    
                    $('#modal-moderate-reply-duplicate-reply-head').show();
                    $('#modal-moderate-reply-duplicate-reply-rows').show();
                }
                
                $('#modal-moderate-reply-duplicate-warn').show();
            } else {
                $('#modal-moderate-reply-duplicate-warn').hide();
                $('#modal-moderate-reply-duplicate-none').show();
            }
            
            UIkit.modal('#modal-moderate-reply').show();
        }
    });
});

$('#modal-moderate-reply-duplicate-warn-toggle').click(function() {
    var toggleIcon = $('#modal-moderate-reply-duplicate-warn-icon');
    
    if (toggleIcon.hasClass('uk-icon-caret-square-o-up')) {
        toggleIcon.removeClass('uk-icon-caret-square-o-up');
        toggleIcon.addClass('uk-icon-caret-square-o-down');
        
        $(this).attr('title', 'Click to Hide Details');
        
        $('#modal-moderate-reply-duplicate-warn-more').show();
    } else {
        toggleIcon.removeClass('uk-icon-caret-square-o-down');
        toggleIcon.addClass('uk-icon-caret-square-o-up');
        
        $(this).attr('title', 'Click to View Details');
        
        $('#modal-moderate-reply-duplicate-warn-more').hide();
    }
});

$('#modal-moderate-reply-reload').click(function() {
    UIkit.modal('#modal-moderate-reply').hide();
    window.location.reload();
});

$('#modal-moderate-reply-banned').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: ($('#modal-moderate-reply-banned').prop('checked')) ? 'addBanned' : 'remBanned',
               params: {
                        bid: '<?php echo $_SESSION['board']->id; ?>',
                   username: ($('#modal-moderate-reply-banned').prop('checked')) ? $('#modal-moderate-reply-username').val() : $('#modal-moderate-reply-userid').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            if ($('#modal-moderate-reply-banned').prop('checked')) {
                UIkit.notify('The user is now banned.', { status: 'info' });
            } else {
                UIkit.notify('The user is no longer banned.', { status: 'info' });
            }
        } else {
            UIkit.notify(data.msg, { status: 'info' });
        }
    });
});

$('#modal-moderate-reply-approved').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: ($('#modal-moderate-reply-approved').prop('checked')) ? 'addApprovedUser' : 'remApprovedUser',
               params: {
                        bid: '<?php echo $_SESSION['board']->id; ?>',
                   username: ($('#modal-moderate-reply-approved').prop('checked')) ? $('#modal-moderate-reply-username').val() : $('#modal-moderate-reply-userid').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            if ($('#modal-moderate-reply-approved').prop('checked')) {
                UIkit.notify('The user is now approved.', { status: 'info' });
            } else {
                UIkit.notify('The user is no longer approved.', { status: 'info' });
            }
        } else {
            UIkit.notify(data.msg, { status: 'info' });
        }
    });
});

var pointsCountReply = 0;
var pointsColorReply = {};

$('[id*="modal-moderate-reply-points-"]').hover(
    function() {
        var num = this.id.substr(this.id.lastIndexOf('-') + 1);
        if (num == 'text') { return; }
        
        if (num > 0) {
            $('#modal-moderate-reply-points-0').css('color', '#000000');
        
            for (i = 1; i <= 3; i++) {
                if (i <= num) {
                    $('#modal-moderate-reply-points-' + i).css('color', '#D85030');
                } else {
                    $('#modal-moderate-reply-points-' + i).css('color', '#000000');
                }
            }
        } else {
            $('#modal-moderate-reply-points-0').css('color', '#D85030');
            $('#modal-moderate-reply-points-1').css('color', '#000000');
            $('#modal-moderate-reply-points-2').css('color', '#000000');
            $('#modal-moderate-reply-points-3').css('color', '#000000');
        }
        
        $('#modal-moderate-reply-points-text').text(num + ' Point' + ((num != 1) ? 's' : ''));
    },
    function() {
        $('#modal-moderate-reply-points-0').css('color', pointsColorReply[0]);
        $('#modal-moderate-reply-points-1').css('color', pointsColorReply[1]);
        $('#modal-moderate-reply-points-2').css('color', pointsColorReply[2]);
        $('#modal-moderate-reply-points-3').css('color', pointsColorReply[3]);
        
        $('#modal-moderate-reply-points-text').text(pointsCountReply + ' Point' + ((pointsCountReply != 1) ? 's' : ''));
    }
);

$('[id*="modal-moderate-reply-points-"]').click(function() {
    var num = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Reply',
               method: 'addModeratorPoints',
               params: {
                      rid: $('#modal-moderate-reply-id').val(),
                      uid: $('#modal-moderate-reply-userid').val(),
                   points: num
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            pointsColorReply[0] = $('#modal-moderate-reply-points-0').css('color');
            pointsColorReply[1] = $('#modal-moderate-reply-points-1').css('color');
            pointsColorReply[2] = $('#modal-moderate-reply-points-2').css('color');
            pointsColorReply[3] = $('#modal-moderate-reply-points-3').css('color');
            
            pointsCountReply = num;
            
            UIkit.notify('The user\'s moderator points have been updated.', { status: 'info' });
        }
    });
});

var mv = $('[name*="mediaVideo"]');

mv.each(function() {
    $(this).get(0).contentWindow.location.href = $(this).attr('src');
});

var goTo = false;

$(document).ready(function() {
    if (window.location.hash.substr(1, 4) == 'open') {
        var top = window.location.hash.substr(5, window.location.hash.indexOf('reply') - 5);
        goTo = window.location.hash.substr(window.location.hash.indexOf('reply') + 5);
        
        window.location.href = '#reply' + top;
        $('#reply-responses-show-' + top).click();
    }
});
