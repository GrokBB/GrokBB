/* G > Topic */

<?php if ($_SESSION['board']->type == 2 && \GrokBB\Board::isApproved($_SESSION['board']->id) == false) { ?>

$('#request-access-submit').click(function() {
    if ($('#editor-request-access').val() == '') {
        UIkit.notify('You must enter a message.', { status: 'info' });
    }
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'requestTopicAccess',
               params: {
                       bid: '<?php echo $_SESSION['board']->id; ?>',
                   message: $('#editor-request-access').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your request has been submitted.', { status: 'info' });
            
            keepAliveClose();
            
            setTimeout(function() {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>';
            }, 1000);
        }
    });
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

$('[id*="topic-category-box-"]').click(function() {
    var cid = this.id.substr(this.id.lastIndexOf('-') + 1);
    $('#topic-category-' + cid).prop('checked', true);
});

$('#editor-create').click(function() {
    $('#topic-create-msg').hide();
    
    if ($('#topic-title').val() == '') {
        $('#topic-create-msg').html('You must enter a title.');
        $('#topic-create-msg').show();
        return;
    }
    
    if ($('#editor-text').val() == '') {
        $('#topic-create-msg').html('You must enter some content.');
        $('#topic-create-msg').show();
        return;
    }
    
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
               method: 'create',
               params: {
                       bid: '<?php echo $_SESSION['board']->id; ?>',
                       cid: $('input[name="topic-category"]:checked').val(),
                     title: $('#topic-title').val(),
                   content: $('#editor-text').val(),
                       rid: $('#topic-reply').val(),
                    sticky: ($('#topic-sticky').prop('checked')) ? 1 : 0,
                   private: ($('#topic-private').prop('checked')) ? 1 : 0,
                     media: media
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
            
        if (data.result) {
            UIkit.notify('Your topic has been created.', { status: 'info' });
            
            keepAliveClose();
            
            setTimeout(function() {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/view/' + data.msg;
            }, 1000);
        } else {
            $('#topic-create-msg').html(data.msg);
            $('#topic-create-msg').show();
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

$('[data-uk-tab]').on('change.uk.tab', function(event, tab){
    switch(tab.attr('id')) {
        case 'topic-create' :
            setTimeout(function() {
                $('#topic-title').focus();
            }, 0);
            break;
    }
});

$('.topic-media-add').click(function() {
    $('#topic-media-all').append('<div class="uk-width-8-10 gbb-spacing topic-media-div"><input class="uk-width-1-1 topic-media-url" type="text" maxlength="2000" value=""><div class="uk-margin-top uk-margin-bottom"><strong>Caption</strong>&nbsp;&nbsp;<input class="uk-width-1-2 topic-media-txt" type="text" maxlength="120" value=""></div></div>');
});

<?php } ?>