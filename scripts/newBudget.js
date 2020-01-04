$(function() {

var user = $('#user').text();

// date picker:
$('.datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
});

// accordion panels
var pnl = $('#pnl').text();
if (pnl !== 'none') {
    //NOT WORKING! document.getElementById(pnl).click();
    if (pnl === 'budget') {
        $('#budget').toggle();
    } else if (pnl === 'cards') {
        $('#cards').toggle();
    } else if (pnl === 'charges') {
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

// initialization of select boxes: 1st is 'old cards'
$('p[id^=oc]').each(function() {
    var selid = "#sel" + this.id;
    var cardType = $(this).text();
    $(selid).val(cardType);
});
// next step is to add the 'name' attribute to the card selects in Expenses tab
$('span[id^=ncd]').each(function() {
    $(this).children().eq(0).attr('name', 'newcds[]');
});
// ditto for the 'old' card selects
$('span[id^=crcd]').each(function() {
    $(this).children().eq(0).attr('name', 'oldcds[]');
});
// now get the initial values for 'old cards' in Expenses tab
var cdvals = [];
$('p[id^=cd]').each(function(indx) {
    cdvals[indx] = $(this).text();
});
// set the select display per values above
$('span[id^=crcd]').each(function(i) {
    $(this).children().eq(0).val(cdvals[i]);
});

// return to budget page button
$('#done').on('click', function(ev) {
    ev.preventDefault();
    $.post('../utilities/setall.php', {user: user});
    var redir = '../main/displayBudget.php?user=' + encodeURIComponent(user);
    window.open(redir, "_self");
});
// save and come back later
$('#lv1').on('click', function(ev) {
    ev.preventDefault();
    $('input[name=lv1]').attr('value', 'yes');
    $('#form').submit();
});
// or, no credit cards to enter
$('#nocds').on('click', function(ev) {
    ev.preventDefault();
    var redir = '../main/displayBudget.php?user=' + encodeURIComponent(user);
    window.open(redir, "_self");
});
$('#lv2').on('click', function(ev) {
    ev.preventDefault();
    $('input[name=lv2]').attr('value', 'yes');
    $('#cdform').submit();
});
$('#lv3').on('click', function(ev) {
    ev.preventDefault();
    $('input[name=lv3]').attr('value', 'yes');
    $('#edform').submit();
});

// data validation (dbValidation.js must already be included in scripts)
// budgets, first panel
var $buddata = $('.bud');
var $baldata = $('.bal');
integerValue($buddata);
scaleTwoNumber($baldata);
// charges, second panel
var $chgdata = $('.amts');
scaleTwoNumber($chgdata);
    
});