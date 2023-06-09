/* User > My Topics */

$('[id*="topic-user-"]').click(function() {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    window.location.href = '<?php echo SITE_BASE_URL; ?>/user/view/' + uid;
});

$('[id*="topic-unsave-"]').click(function() {
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
            UIkit.notify('The topic has been unsaved.', { status: 'info' });
            
            $('#topic-' + tid).hide();
        }
    });
});
