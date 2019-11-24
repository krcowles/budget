$(function() {

var user = $('#user').text();

// accordion panels
var pnl = $('#pnl').text();
if (pnl !== 'none') {
    //NOT WORKING! document.getElementById(pnl).click();
    if (pnl === 'one') {
        $('#budget').toggle();
    } else if (pnl === 'two') {
        $('#cards').toggle();
    } else if (pnl === 'three') {
        $('#expenses').toggle();
    }
}
$('#one').on('click', function() {
    $('#budget').toggle();
    $('#cards').hide();
    $('#expenses').hide();

});
$('#two').on('click', function() {
    $('#cards').toggle();
    $('#budget').hide();
    $('#expenses').hide();
});
$('#three').on('click', function() {
    $('#expenses').toggle();
    $('#budget').hide();
    $('#cards').hide();
});

// initializartion of select boxes
$('p[id^=oc]').each(function() {
    var selid = "#sel" + this.id;
    var cardType = $(this).text();
    $(selid).val(cardType);
});
$('p[id^=em]').each(function() {
    var selid = "#sel" + this.id;
    var method = $(this).text();
    $(selid).val(method);
});

// return to main
$('#done').on('click', function(ev) {
    ev.preventDefault();
    window.open("../main/displayBudget.php?user=" + user, "_self");
});
    
});