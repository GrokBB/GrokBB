/* User > Reset */

$('#reset-send').click(function() {
    $('#reset-username-msg').hide();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'User',
               method: 'reset',
               params: { username: $('#reset-username').val() }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        $('#reset-username-msg').removeClass('uk-alert-success');
        $('#reset-username-msg').removeClass('uk-alert-danger');
        $('#reset-username-msg').html(data.msg);
        
        if (data.result) {
            $('#reset-username-msg').addClass('uk-alert-success');
        } else {
            $('#reset-username-msg').addClass('uk-alert-danger');
        }
        
        $('#reset-username-msg').show();
    });
});
