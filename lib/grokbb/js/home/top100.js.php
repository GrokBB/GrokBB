/* Home > Top 100 */

$('[id*="top100-link-logo"]').hover(
    function() {
        var bid = this.id.substr(this.id.lastIndexOf('-') + 1);
        $('#top100-link-name-' + bid).css('text-decoration', 'underline');
    },
    function() {
        var bid = this.id.substr(this.id.lastIndexOf('-') + 1);
        $('#top100-link-name-' + bid).css('text-decoration', '');
    }
);