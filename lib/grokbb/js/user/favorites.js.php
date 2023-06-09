/* User > Favorites */

$('[id*="favorite-remove-"]').click(function() {
    var bid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'remFavorite',
               params: {
                   id: bid
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('You are no longer subscribed to this board.', { status: 'info' });
            
            setTimeout(function() {    
                window.location.reload(true);
            }, 1000);
        }
    });
});
