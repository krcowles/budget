$(function() {

var no_of_cards = parseInt($('#card_cnt').text()); // already save count
if (no_of_cards > 0) {
    for(var i=0; i<no_of_cards; i++) {
        var selid = "#sel" + i;
        var cdtype = "#type" + i;
        var content = $(cdtype).text();
        $(selid).val(content);
    }
}

// detect any changes to notify user if clicking 'Done' prior to 'Save'
var changes = false; // on page load
$('input').on('change', function() {
    changes = true;
});
$('select').each(function() {
    var selector = "#" + this.id;
    $(document).on('change', selector, function() {
        changes = true;
    });
});

$('#done').on('click', function(ev) {
    ev.preventDefault();
    if (changes) {
        var ans = confirm("Do you wish to save your changes first?\n" +
            "If so, please use the 'Save Data' button");
        if (!ans) {
            window.open("../main/budget.php", "_self");
        }
    } else {
        window.open("../main/budget.php", "_self");
    }
});

});