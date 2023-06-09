/* User > Friends */

$('[id*="friend-view-"]').click(function() {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    window.location.href = '<?php echo SITE_BASE_URL; ?>/user/view/' + uid;
});

$('[id*="friend-remove-"]').click(function() {
    var uid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'User',
               method: 'remFriend',
               params: {
                   uid: uid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('You have removed this user as a friend.', { status: 'info' });
            
            setTimeout(function() {    
                window.location.reload(true);
            }, 1000);
        }
    });
});
