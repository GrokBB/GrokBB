/* Home > Search */

var tagsForSearch = <?php echo (isset($_SESSION['tagsForSearch'])) ? json_encode($_SESSION['tagsForSearch']) : '[]'; ?>;

$('#search-text').keyup(function(e) {
    // ENTER
    if (e.which == 13) {
        $('#search-submit').click();
    }
});

$('#search-submit').click(function() {
    var tags = $('#search-tagbox').tagHandler('getSerializedTags');
    $('#search-tags').val(tags);
    
    $('#search-form').attr('action', '<?php echo SITE_BASE_URL; ?>/home/search');
    $('#search-form').submit();
});

$('#search-tagbox').tagHandler({
        initLoad: false,
        allowAdd: false,
    autocomplete: true,
          getURL: '<?php echo SITE_BASE_URL; ?>/api.php',
         getData: {
             apihash: '<?php echo $_SESSION['apihash']; ?>',
              object: 'Board',
              method: 'getTags',
              params: {}
         },
        minChars: 2,
       queryname: 'query',
    assignedTags: tagsForSearch
});

$('[id*="search-link-logo"]').hover(
    function() {
        var bid = this.id.substr(this.id.lastIndexOf('-') + 1);
        $('#search-link-name-' + bid).css('text-decoration', 'underline');
    },
    function() {
        var bid = this.id.substr(this.id.lastIndexOf('-') + 1);
        $('#search-link-name-' + bid).css('text-decoration', '');
    }
);

$('.tagHandler').on('mouseover', 'li.tagItem', function() {
    $(this).attr('data-uk-tooltip', '{ pos: "bottom-left" }');
    $(this).attr('title', 'Click to Delete Tag');
});

$(document).ready(function() {
    $('.tagInputField').attr('placeholder', 'Search by tags ...');
});