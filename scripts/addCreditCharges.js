$(function() {

// add 'name' attribute to account select drop-down
var noOfCards = $('#cdcnt').text();
for (var j=0; j<noOfCards; j++) {
    var spanid = "#cd" + j;
    for (var k=0; k<4; k++) {
        var selid = spanid + 'it' + k;
        var spanner = $(selid);
        var item = $(selid).children().eq(0);
        $(selid).children().eq(0).attr('name', 'acct[]');
    }
}

$('#back').on('click', function(ev) {
    ev.preventDefault();
    var home = "../main/displayBudget.php?user=" + $('#user').text();
    window.open(home, "_self")
});

});