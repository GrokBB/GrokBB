/* User > Profile */

$(function(){
    var progressbar = $('#avatar-progressbar');
    var bar = progressbar.find('.uk-progress-bar');
    
    var settings = {
        action: '<?php echo SITE_BASE_URL; ?>/api.php',
        params: {
            apihash: '<?php echo $_SESSION['apihash']; ?>',
             object: 'User',
             method: 'avatar',
        },
        
        filelimit: 1,
        allow: '*.(png)',
        
        loadstart: function() {
            bar.css('width', '0%').text('0%');
            progressbar.css('display', 'inline-block');
        },
        
        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css('width', percent + '%').text(percent + '%');
        },

        allcomplete: function(response) {
            bar.css('width', '100%').text('100%');
            
            setTimeout(function(){
                progressbar.hide();
                
                var imgSRC = '<?php echo SITE_BASE_URL; ?>/img.php?uid=<?php echo $_SESSION['user']->id; ?>';
                $('#avatar-image').attr('src', imgSRC + '&time=<?php echo time(); ?>');
            }, 1250);
        }
    };

    UIkit.uploadSelect($('#avatar-select'), settings);
});

$('#editor-save').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'User',
               method: 'setBio',
               params: { bio: $('#editor-text').val() }
               
          }
    }).done(function(data) {
        UIkit.notify('Your updates have been saved.', { status: 'info' });
        
        keepAliveClose();
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