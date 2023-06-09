/* G > Header */

var gbbName = '<?php echo $_SESSION['board']->name; ?>';
$('.gbb-header-text span').text(gbbName);

$('title').text(gbbName + '<?php echo ucwords(str_replace('g/', ' > ', $_SESSION['pagerqst']));?>');

$('#header-search-qry').click(function(event) {
    
    if ($('#header-subnav').css('display') == 'none') {
        <?php if ($_SESSION['sort'] != 'qry') { ?>
        $('#header-search a.uk-button').removeClass('uk-button-primary');
        $(this).addClass('uk-button-primary');
        <?php } ?>
        
        $('.gbb-navbar').append($('#header-subnav'));
        $('#header-subnav').show();
    } else {
        <?php if ($_SESSION['sort'] == 'qry') { ?>
        $('#header-subnav').hide();
        <?php } else { ?>
        window.location.reload(true);
        <?php } ?>
    }
    
    event.stopPropagation();
});

$('#header-search-qry-submit').click(function() {
    var qryUser = $('input[name="qry-user"]:checked').val();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/search/sort/qry'; ?>',
          data: {
              text: $('#qry-type-text').val(),
              type: $('input[name="qry-type"]:checked').val(),
              user: (qryUser == '') ? $('#qry-user-selected').val() : qryUser,
              sort: $('input[name="qry-sort"]:checked').val()
          }
    }).done(function(data) {
        window.location.reload(true);
    });
});

$('#qry-type-text').keyup(function(e) {
    // ENTER
    if (e.which == 13) {
        $('#header-search-qry-submit').click();
    }
});

$('#qry-user-selected').autocomplete({
    minLength: 2,
    source: function(request, response) {
        $.ajax({
            method: 'POST',
               url: '<?php echo SITE_BASE_URL; ?>/api.php',
              data: {
                  apihash: '<?php echo $_SESSION['apihash']; ?>',
                   object: 'User',
                   method: 'search',
                   params: {
                       term: request.term
                   }
              }
        }).done(function(data) {
            var data = $.parseJSON(data);
            
            if (data.result) {
                response(data.msg);
            } else {
                response([]);
            }
        });
    }
});

$('[id*="category-box-"]').click(function() {
    var cid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    if ($('#category-' + cid).prop('checked')) {
        $('#category-' + cid).prop('checked', false);
    } else {
        $('#category-' + cid).prop('checked', true);
    }
});

$('#configure-categories-apply').click(function() {
    var categories = [];
    
    $('#modal-configure-categories input:checked').each(function() {
        var cid = $(this).attr('id').substr($(this).attr('id').lastIndexOf('-') + 1);
        categories.push(cid);
    });
    
    if (categories.length == 0) { categories.push(0); }
    window.location.href = '<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/search/bcat/'; ?>' + categories.join('-');
});

$('[id*="tag-box-"]').click(function() {
    var cid = this.id.substr(this.id.lastIndexOf('-') + 1);
    
    if ($('#tag-' + cid).prop('checked')) {
        $('#tag-' + cid).prop('checked', false);
    } else {
        $('#tag-' + cid).prop('checked', true);
    }
});

$('#configure-tags-apply').click(function() {
    var tags = [];
    
    $('#modal-configure-tags #tag-list-u input:checked').each(function() {
        var tid = $(this).attr('id').substr($(this).attr('id').lastIndexOf('-') + 1);
        tags.push('u' + tid);
    });
    
    $('#modal-configure-tags #tag-list-b input:checked').each(function() {
        var tid = $(this).attr('id').substr($(this).attr('id').lastIndexOf('-') + 1);
        tags.push('b' + tid);
    });
    
    if (tags.length == 0) {
        tags.push(0);
    } else {
        tags.push('u' + $('input[name="uType"]:checked').val());
        tags.push('b' + $('input[name="bType"]:checked').val());
    }
    
    window.location.href = '<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/search/btag/'; ?>' + tags.join('-');
});

var tagsByTopic = <?php echo (isset($_SESSION['tagsByTopic']) && $_SESSION['tagsByTopic']) ? json_encode($_SESSION['tagsByTopic']) : '[]'; ?>;
var tagsModerator = <?php echo (isset($_SESSION['tagsModerator']) && $_SESSION['tagsModerator']) ? json_encode($_SESSION['tagsModerator']) : '[]'; ?>;
var tagsAvailable = <?php echo (isset($_SESSION['tagsAvailable']) && $_SESSION['tagsAvailable']) ? json_encode($_SESSION['tagsAvailable']) : '[]'; ?>;
var tagHandlerDefaults = { autocomplete: true, availableTags: [], minChars: 1 };

for (var tag in tagsAvailable) {
    var tagName = tagsAvailable[tag];
    
    // make sure the tag doesn't already exist, because a duplicate entry will 
    // cause TagHandler to display the tag, even when it is already being used
    if (tagHandlerDefaults.availableTags.indexOf(tagName) == -1) {
        tagHandlerDefaults.availableTags.push(tagName);
    }
}
