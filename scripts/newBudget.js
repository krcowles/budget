/**
 * @fileoverview This script monitors user data entry and checks for
 * completed form data when submitting.
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 * @version 3.0 Modified for transfer to Mochahost
 * @version 3.1 Initialization modifications for sections
 */
$(function() {

/**
 * Some initialization is required...
 */
// Check 'pnl' in query string [#pnlin], if '000' => read and update query string:
if ($('#pnlin').text() === '000') {
    let curr_setup = '?pnl=' + $('#curr_setup').text();
    let url = window.location.href;       
    let urlSplit = url.split( "?" );       
    let obj = { Title : "New Budget Entry", Url: urlSplit[0] + curr_setup};       
    history.pushState(obj, obj.Title, obj.Url);
}

// initialize date picker:
$('.datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
});

// for section 3, items identifying whether or not budget and card data already exist
var nobud = $('#no_bud_dat').text() === 'nodat' ? true : false;
var nocds = $('#no_crd_dat').text() === 'nocrds' ? true : false;
function selcheck() {
    var selsok = true;
    if ($('#eentered').length !== 0) {
        $('span[id^=crcd').each(function() {
            let $crsel = $(this).children().eq(0);
            let choices = $crsel[0].options;
            let opt = $crsel[0].selectedIndex;
            let choice = choices[opt].value;
            if (choice == '') {
                alert("A credit card name has changed: please re-select\n" +
                    "a card for existing charges that are now blank");
                selsok = false;
                return false;
            }
        });
        $('.oldbud').each(function() {
            let accsels = this.options;
            let indx = this.selectedIndex;
            if (indx === -1) {
                alert("A budget account name has changed: please re-select\n" +
                    "an account for existing charges that are now blank");
                selsok = false;
                return false;
            }
        });
    }
    return selsok;
}

// div click behavior
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
    if ($('#expenses').is(':hidden')) {
        var btnstate = true;
        if (nobud && nocds) {
            alert("You must have entered both Budget and Card information\n" +
                "in order to complete this section");
            btnstate = false;
        } else if (nobud) {
            alert("You must have entered budgets to complete this section");
            btnstate = false;
        } else if (nocds) {
            alert("You must have entered cards to complete this section");
            btnstate = false;
        }
        $('#expenses').show();
        if (btnstate) {
            $('#save').removeClass('btnoff');
            $('#save').prop('disabled', false);
            $('#lv3').removeClass('btnoff');
            $('#lv3').prop('disabled', false);
        } else {
            $('#save').addClass('btnoff');
            $('#save').prop('disabled', true);
            $('#lv3').addClass('btnoff');
            $('#lv3').prop('disabled', true);
        }
    } else {
        $('#expenses').hide();
    }
    $('#budget').hide();
    $('#cards').hide();
    var check = selcheck();
});
// initial accordion panel states
if ($('#no_bud_dat').text() === 'nodat') {
    $('#budget').show();
} else if ($('input[name=lv2]').attr('value') === 'no') {
    $('#cards').show();
} else if ($('input[name=lv3]').attr('value') === 'no') {
    document.getElementById("three").click()
}

// initialization of select boxes for existing card data
$('p[id^=oc]').each(function() {
    var selid = "#sel" + this.id;
    var cardType = $(this).text();
    $(selid).val(cardType);
});
// initialization of select boxes for existing expense data
if ($('#eentered').length !== 0) {
    $('div[id^=acctsel').each(function() {
        let pel = $(this).children().eq(1).text();
        let $sel = $(this).children().eq(0);
        $sel.val(pel);
    });
}
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
// set the select display for old cards per values above
$('span[id^=crcd]').each(function(i) {
    $(this).children().eq(0).val(cdvals[i]);
});
// gray out budget select in section three if no budgets entered yet
if ($('#no_bud_dat').text() === 'nodat') {
    $('.budsel').prop('disabled', 'disabled');
}
// validate data in budget section
var $buddata = $('.bud');
var $baldata = $('.bal');
integerValue($buddata); 
scaleTwoNumber($baldata);
// validate data in outstanding expenses:
var $chgdata = $('.amts');
scaleTwoNumber($chgdata);

/**
 * Form submittal validation functions: make sure that line
 * entries are complete.
 */
const dataComplete = (section) => {
    switch (section) {
        case 'one': // are entries made in budgets?
            let lines = [0b000, 0b000, 0b000, 0b000, 0b000];
            let messages = [];
            let $budnames = $('input[class=acctname]');
            let $budamts  = $('input[class=bud]');
            let $budbals  = $('input[class=bal]');
            for (let i=0; i<5; i++) {
                if (!($budnames[i] === '' && $budamts[i] === '' && $budbals[i] === '')) {
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
        case 'two': // no checks are done on credit/debit card names
            return true;
        case 'three': // outstanding credit card expenses
            if (!selcheck()) {
                return false;
            }
            let states = [0b00000, 0b00000, 0b00000, 0b00000];
            let missing    = [];
            // for the new entries only:
            let $wrappers  = $('span[id^=ncd]'); // for cdselects
            let cdselects  = [];
            let $chg_dates = $('.dates');
            let $chg_amts  = $('input[name^=eamt]');
            let $payees    = $('input[name^=epay]');
            let $chgtos    = $('.budsel');
            let actselects = [];
            $wrappers.each(function() {
                let $selbox = $(this).children().eq(0);
                let choices = $selbox[0].options;
                let opt = $selbox[0].selectedIndex;
                let choice = choices[opt].label;
                cdselects.push(choice);
            });
            $chgtos.each(function() {
                let acct  = this.options;
                let aindx = this.selectedIndex;
                let achoice = acct[aindx].label;
                actselects.push(achoice);
            });
            for (let i=0; i<4; i++) { // a check for each of the new data entries
                if (cdselects[i] === 'SELECT ONE:') {
                    states[i] = states[i] | 16;
                }
                if ($chg_dates[i].value == '') {
                    states[i] = states[i] | 8;
                }
                if ($chg_amts[i].value == '') {
                    states[i] = states[i] | 4;
                }
                if ($payees[i].value == '') {
                    states[i] = states[i] | 2;
                }
                if (actselects[i] === 'Select Account') {
                    states[i] = states[i] | 1;
                }
            }
            //let got_old = $('#eentered').length === 0 ? false : true;
            states.forEach(function(state, indx) {
                if (state !== 0 && state !== 31) { // don't bother with lines having no data
                    let e1 = (state & 16) >> 4;
                    let e2 = (state & 8) >> 3;
                    let e3 = (state & 4) >> 2;
                    let e4 = (state & 2) >> 1;
                    let e5 = state & 1;
                    if (e1 == 1 && state !== 15) {
                        missing.push("You have not selected a card for Line " + (indx+1));
                    }
                    if (e2 == 1) {
                        missing.push("You are missing a date on Line " + (indx+1));
                    }
                    if (e3 == 1) {
                        missing.push("You have not entered an amount on Line " + (indx+1));
                    }
                    if (e4 == 1) {
                        missing.push("You have not specified a payee on Line " + (indx+1));
                    }
                    if (e5 == 1) {
                        missing.push("You have not selected an account to charge on Line " +
                            (indx+1));
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

/** 
 * When a user selects 'Save and Continue', data checks
 * may be made prior to saving. The data is refreshed.
 */
$('#save1').on('click', function(ev) {
    ev.preventDefault();
    if(!dataComplete('one')) {
        return;
    }
    $('#form').submit();
    return;
});
$('#save2').on('click', function() {
    $('#cdform').submit();
    return;
});
$('#save').on('click', function(ev) {
    ev.preventDefault();
    if(!dataComplete('three')) {
        return;
    }
    $('input[name=exit3]').attr('value', 'no');
    $('#edform').submit();
    return;
});

/**
 * When a user selects 'Save and Return Later', data checks may
 * be made before saving, and the exitPage is called whereby a
 * link back to this page is provided at the point left
 */
$('#lv1').on('click', function(ev) {
    ev.preventDefault();
    if (!dataComplete('one')) {
        return;
    }
    $('input[name=exit1]').attr('value', 'yes');
    $('#form').submit();
    return;
});
$('#lv2').on('click', function(ev) {
    ev.preventDefault();
    if (!dataComplete('two')) {

        return;
    }
    $('input[name=exit2]').attr('value', 'yes');
    $('#cdform').submit();
    return;
});
$('#lv3').on('click', function(ev) {
    ev.preventDefault();
    if (!dataComplete('three')) {
        return;
    }
    $('input[name=exit3]').attr('value', 'yes');
    $('#edform').submit();
    return;
});

// No credit cards to enter:
$('#nocds').on('click', function(ev) {
    ev.preventDefault();
    // don't do anything if data has already been entered
    if ($('#cold').children().length === 0) {
        // with this choice, section 2 and 3 will be empty
        $.get('../utilities/setall.php')
            .done(function() {
                let budhome = '../main/displayBudget.php';
                window.open(budhome, "_self");
            })
            .fail(function() {
                alert("Failed to update User's data");
            });
    } else {
        alert("Data has already been entered\n" +
            "If you wish, you may delete it");
    }
    return;
});

// force return to budget page
$('#done').on('click', function(ev) {
    ev.preventDefault();
    $.get('../utilities/setall.php')
        .done(function() {
            var redir = '../main/displayBudget.php';
            window.open(redir, "_self");
        })
        .fail(function(results) {
            alert(results);
        });
});
    
});