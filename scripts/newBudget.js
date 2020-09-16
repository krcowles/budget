/**
 * @fileoverview This script monitors user data entry and checks for
 * completed form data when submitting.
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
$(function() {

// date picker:
$('.datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
});

// accordion panels
if ($('input[name=lv1]').attr('value') === 'yes') {
     $('#budget').show();
} else if ($('input[name=lv2]').attr('value') === 'yes') {
    $('#cards').show();
} else if ($('input[name=lv3]').attr('value') === 'yes') {
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

/**
 * Form submittal functions
 */
const dataComplete = (section) => {
    switch (section) {
        case 'one':
            let lines = [0b000, 0b000, 0b000, 0b000, 0b000];
            let messages = [];
            let $budnames = $('input[class=acctname]');
            let $budamts  = $('input[class=bud]');
            let $budbals  = $('input[class=bal]');
            for (let i=0; i<5; i++) {
                if ($budnames[i].value !== '') {
                    lines[i] = lines[i] | 0b100;
                }
                if ($budamts[i].value !== '') {
                    lines[i] = lines[i] | 0b010;
                }
                if ($budbals[i].value !== '') {
                    lines[i] = lines[i] | 0b001;
                }
            }
            lines.forEach(function(state, indx) {
                if (state !== 0) {
                    // apparently can't place bit operators directly in 'if'...
                    let b1 = state >> 2;
                    let b2 = state & 2;
                    let b3 = state & 1;
                    if (b1 === 0) {
                        messages.push("Missing name in Line " + (indx+1));
                    }
                    if (b2 === 0) {
                        messages.push("Missing budget amount in Line " + (indx+1));
                    }
                    if (b3 === 0) {
                        messages.push("Missing Current Value in Line " + (indx+1));
                    }
                }
            });
            let error_list = '';
            for (let j=0; j<messages.length; j++) {
                error_list += messages[j] + "\n";
            }
            if (messages.length > 0) {
                alert(error_list);
                return false;
            } else {
                return true;
            }
        case 'two':
            $card_names = $('input[name^=cname]');
            let cards = false;
            $card_names.each(function() {
                if (this.value !== '') {
                    cards = true;
                    return;
                }
            });
            return cards;
        case 'three':
            let gotcards = true;
            let states = [0b0000, 0b0000, 0b0000, 0b0000];
            let missing    = [];
            let selects    = [];
            let $wrappers  = $('span[id^=ncd]');
            let $chg_dates = $('.dates');
            let $chg_amts  = $('input[name^=eamt]');
            let $payees    = $('input[name^=epay]');
            $wrappers.each(function() {
                let $selbox = $(this).children(":first");
                let choices = $selbox[0].options;
                if (choices.length === 1) {
                    gotcards = false;
                    return;
                }
                let opt = $selbox[0].selectedIndex;
                let choice = choices[opt].label;
                selects.push(choice);
            });
            if (!gotcards) {
                alert("You have not saved any cards yet to which you can charge");
                return false;
            }
            for (let i=0; i<4; i++) {
                if (selects[i] !== 'SELECT ONE:') {
                    states[i] = states[i] | 8;
                }
                if ($chg_dates[i].value !== '') {
                    states[i] = states[i] | 4;
                }
                if ($chg_amts[i].value !== '') {
                    states[i] = states[i] | 2;
                }
                if ($payees[i].value !== '') {
                    states[i] = states[i] | 1;
                }
            }
            states.forEach(function(state, indx) {
                if (state !== 0) {
                    let e1 = state >> 3;
                    let e2 = state & 4;
                    let e3 = state & 2;
                    let e4 = state & 1;
                    if (e1 == 0) {
                        missing.push("You have not selected a card for Line " + (indx+1));
                    }
                    if (e2 == 0) {
                        missing.push("You are missing a date on Line " + (indx+1));
                    }
                    if (e3 == 0) {
                        missing.push("You have not entered an amount on Line " + (indx+1));
                    }
                    if (e4 == 0) {
                        missing.push("You have not specified a payee on Line " + (indx+1));
                    }
                }
            });
            let notes = '';
            for (let j=0; j<missing.length; j++) {
                notes += missing[j] + "\n";
            }
            if (missing.length > 0) {
                alert(notes);
                return false;
            } else {
                return true;
            }
    }
}
$('#save1').on('click', function(ev) {
    ev.preventDefault();
    if(!dataComplete('one')) {
        return;
    }
    $('#form').submit();
    return;
});
$('#save2').on('click', function(ev) {
    ev.preventDefault();
    if(!dataComplete('two')) {
        return;
    }
    $('#cdform').submit();
    return;
});
$('#save').on('click', function(ev) {
    ev.preventDefault();
    if(!dataComplete('three')) {
        return;
    }
    $('#edform').submit();
    return;
});
// set exit status for 'Save and Return Later'
$('#lv1').on('click', function(ev) {
    ev.preventDefault();
    if (!dataComplete('one')) {
        return;
    }
    $('input[name=exit1]').attr('value', 'yes');
    if ($('input[name=lv2]').attr('value') === 'yes' && $('input[name=lv3]').attr('value') === 'yes') {
       alert("You have entered data in all three sections:\n" +
        "You will be able to further edit your data in the main budget page");
    }
    $('#form').submit();
    return;
});
$('#lv2').on('click', function(ev) {
    ev.preventDefault();
    if (!dataComplete('two')) {
        return;
    }
    $('input[name=exit2]').attr('value', 'yes');
    if ($('input[name=lv1]').attr('value') === 'yes' && $('input[name=lv3]').attr('value') === 'yes') {
        alert("You have entered data in all three sections:\n" +
         "You will be able to further edit your data in the main budget page");
    }
    $('cdform').submit();
    return;
});
$('#lv3').on('click', function(ev) {
    ev.preventDefault();
    if (!dataComplete('three')) {
        return;
    }
    $('input[name=exit3]').attr('value', 'yes');
    if ($('input[name=lv1]').attr('value') === 'yes' && $('input[name=lv2]').attr('value') === 'yes') {
        alert("You have entered data in all three sections:\n" +
         "You will be able to further edit your data in the main budget page");
    }
    $('#edform').submit();
    return;
});

// No credit cards to enter:
$('#nocds').on('click', function(ev) {
    ev.preventDefault();
    // don't do anything if data has already been entered
    if ($('#cold').children().length === 0) {
        $('input[name=lv2]').attr('value', 'yes');
        $('input[name=lv3]').attr('value', 'yes');
        $.post('../utilities/noCardSetup.php', {usr: user})
        .fail(function() {
            alert("Failed to update Users table");
        });
        if ($('input[name=lv1]').attr('value') === 'yes') {
            // go to the main budget now
            let budhome = '../main/displayBudget.php';
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
    $.get('../utilities/setall.php').done(function() {
        var redir = '../main/displayBudget.php';
        window.open(redir, "_self");
    }).fail(function(results) {
        alert(results);
    });
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