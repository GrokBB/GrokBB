/* Home > Create */

$('[id*="create-plan-"]').click(function() {
    $('[id*="create-plan-"]').removeClass('uk-panel-box-primary');
    $(this).addClass('uk-panel-box-primary');
    
    $('#' + this.id + '-radio').prop('checked', true);
});

$('[id*="create-type-"]').click(function() {
    $('[id*="create-type-"]').removeClass('uk-panel-box-primary');
    $(this).addClass('uk-panel-box-primary');
    
    $('#' + this.id + '-radio').prop('checked', true);
});

$('#create-new').click(function() {
    $('#create-msg').hide();
    
    if ($('#create-name').val() == '') {
        $('#create-name').focus();
        
        $('#create-msg').html('You must enter a name.');
        $('#create-msg').show();
    } else {
        var params = {
            'plan': 4, /* $('input[name="create-plan"]:checked').val(), */
            'type': $('input[name="create-type"]:checked').val(),
            'name': $('#create-name').val()
        };
        
        $.ajax({
            method: 'POST',
               url: '<?php echo SITE_BASE_URL; ?>/api.php',
              data: {
                  apihash: '<?php echo $_SESSION['apihash']; ?>',
                   object: 'Board',
                   method: 'create',
                   params: params
              }
        }).done(function(data) {
            var data = $.parseJSON(data);
            
            if (data.result) {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/' + $('#create-name').val().replace(/ /g, '_');
            } else {
                $('#create-msg').html(data.msg);
                $('#create-msg').show();
            }
        });
    }
});