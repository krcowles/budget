/**
 * @fileoverview Menu operation in the navbar
 * 
 * @author Ken Cowles
 * @version 2.0 Separated out from budget.js
 * @version 2.1 Corrected and improved valAmt() function
 * @version 3.0 Added check for user activity
 */
/**
 * Check for user activity: all user pages use this menus.js script, 
 * hence it was deemed appropriate for inclusion here instead of adding it
 * as a separate module
 */
const activity_timeout = 15 * 60 * 1000; // 15 minutes of inactivity
var activity = setTimeout(function() {
    $.get('../accounts/logout.php');
    window.open('expired.html', '_self');
}, activity_timeout);
$('body').on('mousemove', function()  {
    clearTimeout(activity);
    activity = setTimeout(function() {
        $.get('../accounts/logout.php');
        window.open('expired.html', '_self');
    }, activity_timeout);
});
$('body').on('keydown', function() {
    clearTimeout(activity);
    activity = setTimeout(function() {
        $.get('../accounts/logout.php');
        window.open('expired.html', '_self');
    }, activity_timeout);
});

// Modal handles:
var expitem = new bootstrap.Modal(document.getElementById('expmodal'));
var depinc  = new bootstrap.Modal(document.getElementById('incmodal'));
var onetdep = new bootstrap.Modal(document.getElementById('othrdeps'));
var delinc  = new bootstrap.Modal(document.getElementById('removeinc'));
var xfrfund = new bootstrap.Modal(document.getElementById('xfrfunds'));
var reccard = new bootstrap.Modal(document.getElementById('reconcile'));
var addcard = new bootstrap.Modal(document.getElementById('addacard'));
var delcard = new bootstrap.Modal(document.getElementById('deletecd'));
var modauto = new bootstrap.Modal(document.getElementById('modap'));
var autopay = new bootstrap.Modal(document.getElementById('auto'));
var delauto = new bootstrap.Modal(document.getElementById('deleteauto'));
var addacct = new bootstrap.Modal(document.getElementById('addacct'));
var delacct = new bootstrap.Modal(document.getElementById('deleteacct'));
var moveacc = new bootstrap.Modal(document.getElementById('moveacct'));
var rename  = new bootstrap.Modal(document.getElementById('renameacct'));
var monthly = new bootstrap.Modal(document.getElementById('moexp'));
var yearly  = new bootstrap.Modal(document.getElementById('annexp'));
var anninc  = new bootstrap.Modal(document.getElementById('anninc'));
var annxfrs = new bootstrap.Modal(document.getElementById('annxfrs'));
var chgpass = new bootstrap.Modal(document.getElementById('resetemail'));
var secques = new bootstrap.Modal(document.getElementById('security'));

// Add 'Undistributed Funds' as an option for 'Move Account'
var $mvlist = $('#mvto .partsel');
var undisopt = document.createElement('option');
undisopt.text = "Undistributed Funds";
$mvlist[0].add(undisopt);
// Disable 'Undistributed Funds' for 'Rename Account':
var $ren = $('#asel .fullsel');
for (var j=0; j<$ren[0].options.length; j++) {
    if ($ren[0].options[j].text == 'Undistributed Funds') {
        $ren[0].options[j].disabled = 'disabled';
        break;
    }
}
// Prep the data for non-monthly income (needed to 'undo' a deposit)
var depositList;
$.get('../utilities/getDeposits.php', function(list) {
    depositList = JSON.parse(list);
    for (let k=0; k<depositList.length; k++) {
        $('#irdeps').append(depositList[k]);
    }
});

// Utility function for autopay modals
const apacctExists = (apacct) => {
    let match = false;
    let indx  = 0;
    for (let j=0; j<existingAPs.length; j++) {
        if (existingAPs[j].acct === apacct) {
            indx = j;
            match = true;
            break;
        }
    }
    results = [match, indx];
    return results;
}
// get date info to check for upcoming or past due autopays
var today = new Date();
var dd = parseInt(String(today.getDate()).padStart(2, '0'));
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January was otherwise 0!
var yyyy = parseInt(today.getFullYear());
var modig = parseInt(mm);
/**
 * All other modal operation
 */
$('#chgexp').on('click', function() {
    // was triggering twice, don't know why, so:
    $('#pebtn').off('click').on('click', function() {
        let $tbl_selects = $('#exptbl').find('tr').find('select'); // modal body data
        let sel1 = getSelect($tbl_selects[0]); // Acct to charge
        let sel2 = getSelect($tbl_selects[1]); // 'Check or Draft' or cd name
        if (sel2 === 'SELECT ONE:') {
            alert("You must select a payment method");
            return false;
        }
        let amtin = "#expamt"; // required in clean_obj, below
        let chgamt = $(amtin).val();
        let amt = valAmt(chgamt, true);
        if (amt < 0) {
            alert("Nagative amount will act as a credit towards " + sel1);
        }
        if (amt === 0) {
            return false;
        }
        let payin = "#exppayto"; // required in clean_obj
        let payee = $(payin).val()
        if (!valPayee(payee)) {
            return false;
        }
        let clean_obj = {ids:[amtin, payin], sels:[$tbl_selects[0], $tbl_selects[1]]};
        let ajaxdata = {id: 'payexp', acct_name: sel1, method: sel2,
            amt: amt, payto: payee};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, expitem, 'stay', clean_obj);
    });
    expitem.show();
});
$('#reginc').on('click', function() {
    $('#incbtn').off('click').on('click', function() {
        let moamt = $('#incdep').val();
        reg = valAmt(moamt, true);
        if (reg === 0) {
            return false;
        }
        let save_url = '../edit/saveAcctEdits.php';
        let ajaxdata;
        if($('#defer').is(':checked')) {
            $('#preloader').show();
            let nxtmo = $('#defermo').text();
            ajaxdata = {id: 'addacct', acct_name: 'Deferred Income', monthly: '0'};
            // don't use 'executeScript()' here!
            $.ajax({
                url: save_url,
                method: 'post',
                data: ajaxdata,
                dataType: 'text',
                success: function() {
                    let defdata = {amt: reg, til: nxtmo};
                    let defurl  = '../edit/setDeferred.php';
                    $.ajax({
                        url: defurl,
                        method: 'post',
                        data: defdata,
                        dataType: 'text',
                        success: function(result) {
                            if (result !== "OK") {
                                alert("Could not deposit funds in 'Deferred Income'");
                            }
                            $('#preloader').hide();
                            depinc.hide();
                            window.open("../main/displayBudget.php");
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            let msgtxt = 'Error in "setDeferred.php";\n';
                            let msg = msgtxt + textStatus + "; Error: " + errorThrown;
                            alert(msg);
                            depinc.hide();
                        }
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    let msgtxt = "Error adding 'Deferred Income' account\n";
                    msg = msgtxt + textStatus + "; Error: " + errorThrown;
                    alert(msg);
                    depinc.hide();
                }
            });
            return;
        } else {
            ajaxdata = {id: 'income', funds: reg};
            executeScript(save_url, ajaxdata, depinc, 'home', {});
        }
    });
    depinc.show();
});
$('#onetimer').on('click', function() {
    $('#otbtn').on('click', function() {
        let otamt = $('#onedep').val();
        funds = valAmt(otamt, true);
        if (funds === 0) {
            return false;
        }
        let otmemo = $('#otmemo').val();
        let ajaxdata = {id: 'otdeposit', newfunds: funds, note: otmemo};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, onetdep, 'home', {});
    });
    onetdep.show();
});
$('#undoinc').on('click', function() {
    delinc.show();
    let $items = $('#irdeps tr').find('input[id^=incitem]');
    $('#selinc').on('click', function() {
        let incids = [];
        $items.each(function() {
            if ($(this).is(":checked")) {
                let $row = $(this).parent().parent();
                let $depid = $row.children().eq(4);
                incids.push($depid.html());
            }
        });
        if (incids.length === 0) {
            alert("You have not selected any deposits");
            return false;
        }
        let depids = {array: JSON.stringify(incids)};
        $.post('../utilities/undoDeposits.php', depids, function() {
            location.reload();
        });
        return;
    });
    return;
});
$('#transfers').on('click', function() {
    $('#xfrbtn').on('click', function() {
        let xframt = $('#xframt').val();
        amt = valAmt(xframt, true);
        if (amt === 0) {
            return false;
        }
        let $from = $('#xfrfrom').children();
        let xfrom = getSelect($from[0]);
        let $to   = $('#xfrto').children();
        let xfrto = getSelect($to[0]);
        if (xfrom == xfrto) {
            alert("You have specified the same account for 'Take from' and 'Place in'");
            return false;
        }
        let xfrdata  ={from: xfrom, to: xfrto, amt: amt};
        $.post('../utilities/transfers.php', xfrdata, function(result) {
            if (result !== 'ok') {
                alert("Failed to register transfer in database");
            }
        });
        let ajaxdata = {id: 'xfr', from: xfrom, to: xfrto, sum: amt};
        //executeScript('../edit/saveAcctEdits.php', ajaxdata, xfrfund, 'home', {});
    });
    xfrfund.show();
});
$('#cd2rec').on('click', function() {
    $('#recbtn').on('click', function() {
        let $cardsel = $('#ccsel0').children();
        let card = getSelect($cardsel[0]);
        if (card === 'SELECT ONE:') {
            alert("You must select a credit card");
            return false;
        }
        let recurl = "../utilities/reconcile.php?card=" + card;
        window.open(recurl, "_self");
    });
    reccard.show();
});
$('#addcrdr').on('click', function() {
    $('#addcdbtn').on('click', function() {
        let cname = $('#cda').val();
        cname = cname.trim();
        if (!valText(cname, 'a card name')) {
            return false;
        }
        let ctype = document.getElementById('cdprops');
        let type = getSelect(ctype);
        let ajaxdata = {id: 'addcd', cdname: cname, cdtype: type};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, addcard, 'stay', {});
    });
    addcard.show();
});
$('#dac').on('click', function() {
    let $delitem = $('#deletecard').children();
    $delitem[0].options[1].disabled = true;
    $('#dcbtn').on('click', function() {
        let todelete = getSelect($delitem[0]);
        if (todelete == 'SELECT ONE:') {
            alert("You must specify a card");
            return false;
        }
        let ajaxdata = {id: 'decard', target: todelete};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, delcard, 'stay', {});
    });
    delcard.show();
});
$('#modauto').on('click', function() {
    let $apchoices = $('#maselap').children();
    $apchoices[0].options[1].disabled = true;
    $('#modapbtn').on('click', function() {
        let $moditem   = $('#mapitem').children();
        let modifyap   = getSelect($moditem[0]);
        // does this account have an existing autopay?
        let acctCheck = apacctExists(modifyap);
        // advise immediately if not an existing autopay account
        if (!acctCheck[0]) {
            alert("This account has no existing autopay:\nNo modifications can be made");
            return false;
        }
        let $modmethod = $('#maselap').children();
        let modmeans   = getSelect($modmethod[0]);
        if (modmeans === 'SELECT ONE:') {
            alert('You must select a payment means');
            return false;
        }
        let modday     = $('#newapday').val();
        modday = modday.trim();
        if (!valText(modday, 'a day of the month')) {
            return false;
        }
        if (!valDay(modday)) {
            return false;
        }
        if (acctCheck[0]) {
            if (existingAPs[acctCheck[1]].paid && modday > dd) {
                msg = "NOTE: " + modifyap + " has already been paid for the current month";
                alert(msg);
            }
        } 
        let ids = ['#newapday'];
        let sels = [$moditem[0], $modmethod[0]];
        let ajaxdata = {id: 'apmod', acct: modifyap, method: modmeans, day: modday};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, modauto, 'stay', {ids: ids, sels: sels});
    });
    modauto.show();
});
$('#addauto').on('click', function() {
    let $card = $('#ccselap').children();
    $card[0].options[1].disabled = true;
    $('#addapbtn').on('click', function() {
        let $addap = $('#apsel').children();
        let apadder = getSelect($addap[0]);
        // is there already an autopay specified?
        let acctCheck = apacctExists(apadder);
        if (acctCheck[0]) {
            let msg = "There is already an autopay specified for this account\n";
            msg += "You may use Modify Autopay instead";
            alert(msg);
            return false;
        }
        let card = getSelect($card[0]);
        if (card === 'SELECT ONE:') {
            alert("You must select a means for payment");
            return false;
        }
        let day = $('#useday').val();
        day = day.trim();
        if (!valText(day, 'a day of the month')) {
            return false;
        }
        if (!valDay(day)) {
            return false;
        }
        let ajaxdata = {id: 'apset', acct: apadder, method: card, day: day};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, autopay, 'home', {});
    });
    autopay.show();
});
$('#rmap').on('click', function() {
    $('#delapbtn').on('click', function() {
        let $delap = $('#delapacct').children();
        let delapp = getSelect($delap[0]);
        // is there an autopay specified for this account?
        let acctCheck = apacctExists(delapp);
        if (!acctCheck[0]) {
            let msg = "There is no autopay specified for this account";
            alert(msg);
            return false;
        }
        let ajaxdata = {id: 'delapay', acct: delapp};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, delauto, 'home', {});
    });
    delauto.show();
});
$('#add1').on('click', function() {
    $('#addactbtn').on('click', function() {
        let newname = $('#newacct').val();
        newname = newname.trim();
        if (!valText(newname, ' a name for the account')) {
            return false;
        }
        let budval  = $('#mo').val();
        budamt = valAmt(budval, false);
        if (budamt === 0) {
            return false;
        }
        let ajaxdata = {id: 'addacct', acct_name: newname, monthly: budamt}
        executeScript('../edit/saveAcctEdits.php', ajaxdata, addacct, 'home', {});
    });
    addacct.show();
});
$('#combo').on('click', function() {
    window.open('../edit/combo.php', "_blank");
});
$('#del1').on('click', function() {
    $('#daccbtn').on('click', function() {
        let $acct0 = $('#remacct').children();
        let acct0 = getSelect($acct0[0]);
        let ajaxdata = {id: 'acctdel', type: 'norm', acct: acct0};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, delacct, 'home', {});
    });
    delacct.show();
});
$('#moveit').on('click', function() {
    $('#mvbtn').on('click', function() {
        let $from = $('#mvfrom').children();
        let $to   = $('#mvto').children();
        let from  = getSelect($from[0]);
        let to    = getSelect($to[0]);
        if (from === to) {
            alert("You must specify two different accounts");
            return false;
        }
        let ajaxdata = {id: 'move', mvfrom: from, mvto: to};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, moveacc, 'home', {});
    });
    moveacc.show();
});
$('#ren1').on('click', function() {
    $('#renbtn').on('click', function() {
        let $acct = $('#asel').children();
        let acct  = getSelect($acct[0]);
        let newname = $('#newname').val();
        newname = newname.trim();
        if (!valText(newname, ' a new account name')) {
            return false;
        }
        let ajaxdata = {id: 'rename', acct: acct, newname: newname};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, rename, 'home', {});
    });
    rename.show();
});
$('#mexpense').on('click', function() {
    $('#mexpbtn').on('click', function() {
        let month = document.getElementById('rptmo');
        let rptmo = getSelect(month);
        var getdata = '../utilities/reports.php?id=morpt&mo=' + rptmo;
        window.open(getdata, "_blank");
        monthly.hide();
    });
    monthly.show();
});
$('#annual').on('click', function() {
    $('#anexpbtn').on('click', function() {
        let rptyr = document.getElementById('rptyr');
        let year  = getSelect(rptyr);
        let getdata = '../utilities/reports.php?id=yrrpt&yr=' + year;
        window.open(getdata, "_blank");
        yearly.hide();
    });
    yearly.show();
});
$('#yrinc').on('click', function() {
    $('#aincbtn').on('click', function() {
        let incyr = document.getElementById('incyear');
        let year  = getSelect(incyr);
        let getdata = '../utilities/reports.php?id=inc&incyr=' + year;
        window.open(getdata, "_blank");
        anninc.hide();
    });
    anninc.show();
});
$('#xfrrpt').on('click', function() {
    $('#yrlyXfrbtn').on('click', function() {
        let xfryr = document.getElementById('xfryr');
        let year = getSelect(xfryr);
        let getxfrs = '../utilities/reports.php?id=xfr&xfryr=' + year;
        window.open(getxfrs, "_blank");
        annxfrs.hide();
    });
    annxfrs.show();
});

$('#logout').on('click', function() {
    $('#preloader').show();
    $.ajax({
        url: '../accounts/logout.php',
        method: 'get',
        success: function() {
            $('#preloader').hide();
            window.open('../index.php', "_self");
        }
    });
});
$('#rpass').on('click', function() {
    chgpass.show();
});
$('#cpass').on('click', function() {
    let eaddr = $('#remail').val();
    eaddr = eaddr.toLowerCase();
    if (eaddr === '') {
        alert("You must enter a valid email address");
        return false;
    }
    let adata = {email: eaddr};
    $('#preloader').show();
    $.ajax({
        url: '../accounts/sendmail.php',
        method: 'post',
        data: adata,
        dataType: 'text',
        success: function(result) {
            $('#preloader').hide();
            alert('An email has been sent');
            chgpass.hide();
            $.ajax({
                url: '../accounts/logout.php',
                method: 'get',
                success: function() {
                    alert("You are logged out until the new password is entered");
                    window.open('../index.php', "_self");
                },
                error: function() {
                    let msgtxt = "Error logging out:\n";
                    let msg = msgtxt + textStatus + "; Error: " + errorThrown;
                    alert(msg);
                }
            }); 
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('#preloader').hide();
            let msgtxt = "Error sending email:\n";
            let msg = msgtxt + textStatus + "; Error: " + errorThrown;
            alert(msg);
        }
    });
});
/**
 * Security Questions Modal operation
 */
const requiredAnswers = 3;
/**
 * This function  counts the number of security questions and returns
 * true is correct, false (with user alers) if not
 * 
 * @return {boolean}
 */
const countAns = () => {
     var acnt = 0;
     $('input[id^=q]').each(function() {
         if ($(this).val() !== '') {
             acnt++
         }
     });
     if (acnt > requiredAnswers) {
         alert("You have supplied more than " + requiredAnswers + " answers");
         return false;
     } else if (acnt < requiredAnswers) {
         alert("Please supply answers to " + requiredAnswers + " questions");
         return false;
     } else {
         return true;
     }
 }
$('#ed_sec').on('click', function() {
    $('#uques').empty();
    $.get('../accounts/usersQandA.php', function(body) {
        $('#uques').append(body);
        secques.show();
    }, "html");
});
$('#resetans').on('click', function() {
    $('input[id^=q]').each(function() {
        $(this).val("");
    });
});
$('#closesec').on('click', function() {
    modq = [];
    moda = [];
    if (countAns()) {
        $('input[id^=q]').each(function() {
            var answer = $(this).val();
            if (answer !== '') {
                let qid = this.id;
                qid = qid.substring(1);
                modq.push(qid);
                answer = answer.toLowerCase();
                moda.push(answer);
            }
        });
        let ques = modq.join();
        let uans = moda.join("|");
        let ajaxdata = {questions: ques, answers: uans};
        $.post('../accounts/updateQandA.php', ajaxdata, function(result) {
            if (result === 'ok') {
                alert("Updated Security Questions");
            } else {
                alert("Error: could not update Security Questions");
            }
        }, "text");
        secques.hide();
    }
});

// Initial setting in Help Menu for cookies
var choice = $('#usercookies').text();
$('#chglink').text(choice);
// Change the cookie setting (in db and on menu)
$('#chgcookie').on('click', function() {
    let uchoice = $('#chglink').text();
    let newchoice;
    let newmenu;
    if (uchoice.indexOf('Reject') !== -1) {
        newchoice = 'reject';
        newmenu = 'Accept Cookies';
    } else {
        newchoice = 'accept';
        newmenu = 'Reject Cookies';
    }
    $.get("registerCookieChoice.php", {choice: newchoice}).done(function() {
        $('#chglink').text(newmenu);
        alert("You're new cookie choice has been saved");
    });
});

/**
 * Retrieve selected drop-down text (not value) 
 * 
 * @param {Node} domsel The DOM node of the select box
 * 
 * @returns {string}
 */
function getSelect(domsel) {
    let indx = domsel.selectedIndex;
    let item = domsel.options[indx].label
    return item;
}
/**
 * This is a general purpose function to execute ajax based on input arguments
 * 
 * @param {string} url 
 * @param {object} ajaxdata 
 * @param {modal_handle} modal_handle 
 * @param {string} loc 
 * @param {object} cleanup 
 * 
 * @returns {null}
 */
function executeScript(url, ajaxdata, modal_handle, loc, cleanup) {
    $('#preloader').show();
    $.ajax({
        url: url,
        method: "POST",
        data: ajaxdata,
        dataType: "text",
        success: function(results) {
            if (results === "OK") {
                $('#preloader').hide();
                if (Object.keys(cleanup).length > 0) {
                    resetModalContents(cleanup);
                }
                if (loc === 'stay') {
                    location.reload();
                } else {
                    window.open("../main/displayBudget.php", "_self");
                }
            } else {
                alert("Problem encountered; Operation did not complete");
                modal_handle.hide();
                $('#preloader').hide();
            }
        
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('#preloader').hide();
            let msgtxt = "executeSript() error:\n";
            let msg = msgtxt + textStatus + "; Error: " + errorThrown;
            alert(msg);
            modal_handle.hide();
        }
    });
    return;
}
// ------- Data Validation Functions ------ //
const dollars = "Amount must be in dollars";
const cents   = " (or dollars.cents)\nCents must contain one or two digits only";

/**
 * Validate the amount entered:
 * Dollars must be at least one digit (no decimal value); Dollar.cents must be at least
 * one digit followed by an optional '.' and optionally 1 or 2 digits
 * 
 * @param {string}  user_entry    Value (as string) entered by user
 * @param {boolean} cents_allowed May/may not have cents value
 * 
 * @returns {number}
 */
function valAmt(user_entry, cents_allowed) {
    let amt = user_entry.trim();
    if (amt === '') {
        alert("There is no entry for Amount");
        return 0;
    }
    let test_amt = Math.abs(amt);
    pattern = cents_allowed ? /^\d+\.?[0-9]?[0-9]?$/ : /^\d+$/;
    msg = cents_allowed ? dollars + cents : dollars + " only";
    if (pattern.test(test_amt) === false) {
        alert(msg + "\nNo commas or other non-numeric characters are allowed");
            return 0;
    }
    let checkval = parseFloat(amt);
    if (checkval === 0) {
        alert("An entry amount of 0 will not be applied");
        return 0;
    }
    return checkval;
}
/**
 * Validate that there is an entry for payee
 * 
 * @param {string} payee 
 * 
 * @returns {boolean} 
 */
function valPayee(payee) {
    if (payee === '') {
        alert("You have not entered a payee");
        return false;
    }
    return true;
}
/**
 * Validate that there is text (not an empty string)
 * 
 * @param {string} item 
 * @param {string} type 
 * 
 * @returns {boolean}
 */
function valText(item, type) {
    if (item === '') {
        alert("You must enter " + type);
        return false;
    }
    return true;
}
function valDay(day) {
    if (isNaN(day)) {
        alert("Please enter a numeric value from 1 to 28");
        return false;
    }
    let testday = parseFloat(day);
    if (!Number.isInteger(testday)) {
        alert("Please enter an integer value from 1 to 28");
        return false;
    } else if (testday < 1) {
        alert("The day of the month cannot be 0 or negative");
        return false;
    } else if (testday > 28) {
        let msg = "This day will not work in some months: \n";
        msg += "Please enter a day of the month from 1 to 28";
        alert(msg);
        return false;
    }
    return true;
}
/**
 * Clear out previous entries in a modal
 * 
 * @param {string[]} items 
 * 
 * @returns {null}
 */
function resetModalContents(items) {
    let jqids  = items.ids;
    let jqsels = items.sels;
    for (let j=0; j<jqids.length; j++) {
        $(jqids[j]).val("");
    }
    for (let k=0; k<jqsels.length; k++) {
        jqsels[k].selectedIndex = 0;
    }
}
