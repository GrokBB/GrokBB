/* Signup / Login */

$('#modal-login').on({
    'show.uk.modal' : function() {
        $('#username-login').focus();
    }
});

$('#username-signup').keyup(function() {
    var params = { 'username': $(this).val() };
    
    $('#username-signup-msg').hide();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'User',
               method: 'validateUsername',
               params: params
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        $('#username-signup-msg').removeClass('uk-alert-success');
        $('#username-signup-msg').removeClass('uk-alert-danger');
        $('#username-signup-msg').html(data.msg);
        
        if (data.result) {
            $('#username-signup-msg').addClass('uk-alert-success');
        } else {
            $('#username-signup-msg').addClass('uk-alert-danger');
        }
        
        $('#username-signup-msg').show();
    });
});

$('#password-signup,#password-signup-verify').keyup(function() {
    $('#signup-msg').hide();
    
    if ($('#password-signup-verify').val() != '') {
        if ($('#password-signup-verify').val() != $('#password-signup').val()) {
            $('#signup-msg').html('Your passwords don\'t match.');
            $('#signup-msg').show();
        } else {
            var params = { 'password': $(this).val() };
            
            $.ajax({
                method: 'POST',
                   url: '<?php echo SITE_BASE_URL; ?>/api.php',
                  data: {
                      apihash: '<?php echo $_SESSION['apihash']; ?>',
                       object: 'User',
                       method: 'validatePassword',
                       params: params
                  }
            }).done(function(data) {
                var data = $.parseJSON(data);
                
                if (!data.result) {
                    $('#signup-msg').html(data.msg);
                    $('#signup-msg').show();
                }
            });
        }
    }
});

$('#signup').click(function() {
    $('#signup-msg').hide();
    
    if ($('#username-signup').val() == '') {
        $('#username-signup').focus();
        
        $('#signup-msg').html('You must enter a username.');
        $('#signup-msg').show();
    } else if ($('#password-signup').val() == '' || $('#password-signup-verify').val() == '') {
        if ($('#password-signup').val() == '') {
            $('#password-signup').focus();
        } else if ($('#password-signup-verify').val() == '') {
            $('#password-signup-verify').focus();
        }
        
        $('#signup-msg').html('You must enter matching passwords.');
        $('#signup-msg').show();
    } else {
        if ($('#password-signup-verify').val() != $('#password-signup').val()) {
            $('#password-signup-verify').focus();
            $('#password-signup-verify').select();
            
            $('#signup-msg').html('Your passwords don\'t match.');
            $('#signup-msg').show();
        } else {
            var params = {
                'username': $('#username-signup').val(),
                'password': $('#password-signup').val(),
                'remember': ($('#remember-signup').prop('checked')) ? 1 : 0,
                'email': $('#email-signup').val()
            };
            
            $.ajax({
                method: 'POST',
                   url: '<?php echo SITE_BASE_URL; ?>/api.php',
                  data: {
                      apihash: '<?php echo $_SESSION['apihash']; ?>',
                       object: 'User',
                       method: 'create',
                       params: params
                  }
            }).done(function(data) {
                var data = $.parseJSON(data);
                
                if (data.result) {
                    window.location.reload(true);
                } else {
                    $('#signup-msg').html(data.msg);
                    $('#signup-msg').show();
                }
            });
        }
    }
});

$('#login').click(function() {
    $('#login-msg').hide();
    
    if ($('#username-login').val() == '') {
        $('#username-login').focus();
        
        $('#login-msg').html('You must enter a username.');
        $('#login-msg').show();
    } else if ($('#password-login').val() == '') {
        $('#password-login').focus();
        
        $('#login-msg').html('You must enter a password.');
        $('#login-msg').show();
    } else {
        var params = {
            'username': $('#username-login').val(),
            'password': $('#password-login').val(),
            'remember': ($('#remember-login').prop('checked')) ? 1 : 0
        };
        
        $.ajax({
            method: 'POST',
               url: '<?php echo SITE_BASE_URL; ?>/api.php',
              data: {
                  apihash: '<?php echo $_SESSION['apihash']; ?>',
                   object: 'User',
                   method: 'login',
                   params: params
              }
        }).done(function(data) {
            var data = $.parseJSON(data);
            
            if (data.result) {
                window.location.reload(true);
            } else {
                $('#password-login').focus();
                $('#password-login').select();
                
                $('#login-msg').html(data.msg);
                $('#login-msg').show();
            }
        });
    }
});

$('#login-mobile').click(function() {
    $('#modal-login-signup').hide();
    $('#modal-login-login').show();
});

$('#signup-mobile').click(function() {
    $('#modal-login-login').hide();
    $('#modal-login-signup').removeClass('uk-visible-large');
    $('#modal-login-signup').show();
});

<?php if ($_SESSION['user']) { ?>

/* Settings */

$('#modal-settings').on({
    'show.uk.modal' : function() {
        $('#password-current').focus();
    }
});

$('#settings').click(function() {
    $('#settings-msg').hide();
    
    if ($('#password-current').val() == '') {
        $('#password-current').focus();
        
        $('#settings-msg').html('You must enter your current password.');
        $('#settings-msg').show();
    } else {
        if ($('#password-new-verify').val() != $('#password-new').val()) {
            $('#password-new-verify').focus();
            $('#password-new-verify').select();
            
            $('#settings-msg').html('Your new passwords don\'t match.');
            $('#settings-msg').show();
        } else {
            var params = {
                'username': '<?php echo $_SESSION['user']->username; ?>',
                'password': $('#password-current').val(),
                'password-new': $('#password-new').val(),
                'remember': ($('#remember-new').prop('checked')) ? 1 : 0,
                'email': $('#email').val()
            };
            
            $.ajax({
                method: 'POST',
                   url: '<?php echo SITE_BASE_URL; ?>/api.php',
                  data: {
                      apihash: '<?php echo $_SESSION['apihash']; ?>',
                       object: 'User',
                       method: 'update',
                       params: params
                  }
            }).done(function(data) {
                var data = $.parseJSON(data);
                
                if (data.result) {
                    window.location.href = '<?php echo SITE_BASE_URL; ?>/#success';
                    if (window.location.hash != '') { window.location.reload(true); }
                } else {
                    $('#settings-msg').html(data.msg);
                    $('#settings-msg').show();
                }
            });
        }
    }
});

<?php } ?>