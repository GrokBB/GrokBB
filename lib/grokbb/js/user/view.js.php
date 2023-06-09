/* User > View */

$('#user-friend-add').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'User',
               method: 'addFriend',
               params: {
                    uid: <?php echo (int) $_SESSION['objectid']; ?>
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            UIkit.notify('You have added this user as a friend.', { status: 'info' });
            
            $('#user-friend-add').hide();
            $('#user-friend-rem').show();
        }
    });
    <?php } ?>
});

$('#user-friend-rem').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'User',
               method: 'remFriend',
               params: {
                    uid: <?php echo (int) $_SESSION['objectid']; ?>
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            UIkit.notify('You have removed this user as a friend.', { status: 'info' });
            
            $('#user-friend-rem').hide();
            $('#user-friend-add').show();
        }
    });
    <?php } ?>
});

$('#user-send-message').click(function() {
    <?php if (!isset($_SESSION['user'])) { ?>
    UIkit.modal('#modal-login').show();
    <?php } else { ?>
    window.location.href = '<?php echo SITE_BASE_URL; ?>/user/messages/u<?php echo (int) $_SESSION['objectid']; ?>/#messages-create';
    <?php } ?>
});
