/* User > My Replies */

$('[id*="reply-user-"]').click(function() {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    window.location.href = '<?php echo SITE_BASE_URL; ?>/user/view/' + uid;
});

$('[id*="reply-unsave-"]').click(function() {
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
            UIkit.notify('The reply has been unsaved.', { status: 'info' });
            
            $('#reply-' + rid).hide();
        }
    });
});
