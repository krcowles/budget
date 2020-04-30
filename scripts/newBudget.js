$(function() {

var user = $('#user').text();

// date picker:
$('.datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
});

// accordion panels

if ($('input[name=lv1]').attr('value') === 'no') {
     $('#budget').show();
} else if ($('input[name=lv2]').attr('value') === 'no') {
    $('#cards').show();
} else if ($('input[name=lv3]').attr('value') === 'no') {
    $('#expenses').show();
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

// set exit status for 'Save and Return Later'
$('#lv1').on('click', function() {
    $('input[name=exit1]').attr('value', 'yes');
    if ($('input[name=lv2]').attr('value') === 'yes' && $('input[name=lv3]').attr('value') === 'yes') {
       alert("You have entered data in all three sections:\n" +
        "You will be able to further edit your data in the main budget page");
    }
});
$('#lv2').on('click', function() {
    $('input[name=exit2]').attr('value', 'yes');
    if ($('input[name=lv1]').attr('value') === 'yes' && $('input[name=lv3]').attr('value') === 'yes') {
        alert("You have entered data in all three sections:\n" +
         "You will be able to further edit your data in the main budget page");
    }
});
$('#lv3').on('click', function() {
    $('input[name=exit3]').attr('value', 'yes');
    if ($('input[name=lv1]').attr('value') === 'yes' && $('input[name=lv2]').attr('value') === 'yes') {
        alert("You have entered data in all three sections:\n" +
         "You will be able to further edit your data in the main budget page");
    }
});

// No credit cards to enter:
$('#nocds').on('click', function(ev) {
    ev.preventDefault();
    // don't do anything if data has already been entered
    if (typeof $('#oc0') == 'undefined' || typeof $('#oc0') == 'null') {
        $('input[name=lv2]').attr('value', 'yes');
        $('input[name=lv3]').attr('value', 'yes');
        $.post('../utilities/noCardSetup.php', {usr: user})
        .fail(function() {
            alert("Failed to update Users table");
        });
        if ($('input[name=lv1]').attr('value') === 'yes') {
            // go to the main budget now
            let budhome = '../main/displayBudget.php?user=' + encodeURIComponent(user);
            window.open(budhome, "_self");
        } else {
            alert("Please complete the first section");
        }
    } else {
        alert("Data has already been entered");
    }
});

// return to budget page button
$('#done').on('click', function(ev) {
    ev.preventDefault();
    let $state = $('input[name^=lv]');
    let proceed = false;
    $state.each(function() {
        if ($(this).attr('value') !== 'yes') {
            proceed = false;
            return;
        }
    });
    if (proceed) {
        $.post('../utilities/setall.php', {user: user});
        var redir = '../main/displayBudget.php?user=' + encodeURIComponent(user);
        window.open(redir, "_self");
    } else {
        alert ("You must complete all three sections");
    }
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