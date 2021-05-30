/**
 * @fileoverview Menu operation in the navbar
 * 
 * @author Ken Cowles
 * @version 2.0 Separated out from budget.js
 */
// Modal handles:
var apitems = new bootstrap.Modal(document.getElementById('apmodal'));
var expitem = new bootstrap.Modal(document.getElementById('expmodal'));
var depinc  = new bootstrap.Modal(document.getElementById('incmodal'));
var onetdep = new bootstrap.Modal(document.getElementById('othrdeps'));
var delinc  = new bootstrap.Modal(document.getElementById('removeinc'));
var xfrfund = new bootstrap.Modal(document.getElementById('xfrfunds'));
var reccard = new bootstrap.Modal(document.getElementById('reconcile'));
var addcard = new bootstrap.Modal(document.getElementById('addacard'));
var delcard = new bootstrap.Modal(document.getElementById('deletecd'));
var autopay = new bootstrap.Modal(document.getElementById('auto'));
var delauto = new bootstrap.Modal(document.getElementById('deleteauto'));
var addacct = new bootstrap.Modal(document.getElementById('addacct'));
var delacct = new bootstrap.Modal(document.getElementById('deleteacct'));
var moveacc = new bootstrap.Modal(document.getElementById('moveacct'));
var rename  = new bootstrap.Modal(document.getElementById('renameacct'));
var monthly = new bootstrap.Modal(document.getElementById('moexp'));
var yearly  = new bootstrap.Modal(document.getElementById('annexp'));
var anninc  = new bootstrap.Modal(document.getElementById('anninc'));
var chgpass = new bootstrap.Modal(document.getElementById('resetemail'));

// Add option for 'Move Account'
var $mvlist = $('#mvto .partsel');
var undisopt = document.createElement('option');
undisopt.text = "Undistributed Funds";
$mvlist[0].add(undisopt);
// disable Undistributed Funds for 'Rename Account':
var $ren = $('#asel .fullsel');
for (var j=0; j<$ren[0].options.length; j++) {
    if ($ren[0].options[j].text == 'Undistributed Funds') {
        $ren[0].options[j].disabled = 'disabled';
        break;
    }
}
/**
 * Prep the data for non-monthly income (needed to 'undo' a deposit)
 */
var depositList;
$.get('../utilities/getDeposits.php', function(list) {
    depositList = JSON.parse(list);
    for (let k=0; k<depositList.length; k++) {
        $('#irdeps').append(depositList[k]);
    }
});
 
/**
 * All other modal operation
 */
$('#chgexp').on('click', function() {
    // was triggering twice, don't know why, so:
    $('#pebtn').off('click').on('click', function() {
        let $tblrows = $('#exptbl').find('tr').find('select');
        let sel1 = getSelect($tblrows[0]);
        let sel2 = getSelect($tblrows[1]);
        if (sel2 === 'SELECT ONE:') {
            alert("You must select a payment method");
            return false;
        }
        let amt = $('#expamt').val()
        if (!valAmt(amt)) {
            return false;
        }
        let payee = $('#exppayto').val()
        if (!valPayee(payee)) {
            return false;
        }
        let ajaxdata = {id: 'payexp', acct_name: sel1, method: sel2,
            amt: amt, payto: payee};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, expitem, 'stay');
    });
    expitem.show();
});
$('#reginc').on('click', function() {
    $('#incbtn').off('click').on('click', function() {
        let reg = $('#incdep').val();
        if (!valAmt(reg)) {
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
            executeScript(save_url, ajaxdata, depinc, 'home');
        }
    });
    depinc.show();
});
$('#onetimer').on('click', function() {
    $('#otbtn').on('click', function() {
        let funds = $('#onedep').val();
        if (!valAmt(funds)) {
            return false;
        }
        let otmemo = $('#otmemo').val();
        let ajaxdata = {id: 'otdeposit', newfunds: funds, note: otmemo};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, onetdep, 'home');
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
        let amt = $('#xframt').val();
        if (!valAmt(amt)) {
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
        let ajaxdata = {id: 'xfr', from: xfrom, to: xfrto, sum: amt};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, xfrfund, 'home');
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
        if (!valText(cname, 'a card name')) {
            return false;
        }
        let ctype = document.getElementById('cdprops');
        let type = getSelect(ctype);
        let ajaxdata = {id: 'addcd', cdname: cname, cdtype: type};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, addcard, 'stay');
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
        executeScript('../edit/saveAcctEdits.php', ajaxdata, delcard, 'stay');
    });
    delcard.show();
});
$('#addauto').on('click', function() {
    let $card = $('#ccselap').children();
    $card[0].options[1].disabled = true;
    $('#addapbtn').on('click', function() {
        let $addap = $('#apsel').children();
        let apadder = getSelect($addap[0]);
        let card = getSelect($card[0]);
        if (card === 'SELECT ONE:') {
            alert("You must select a means for payment");
            return false;
        }
        let day = $('#useday').val();
        if (!valText(day, 'a day of the month')) {
            return false;
        }
        let ajaxdata = {id: 'apset', acct: apadder, method: card, day: day};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, autopay, 'home');
    });
    autopay.show();
});
$('#rmap').on('click', function() {
    $('#delapbtn').on('click', function() {
        let $delap = $('#delapacct').children();
        let delapp = getSelect($delap[0]);
        let ajaxdata = {id: 'delapay', acct: delapp};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, delauto, 'home');
    });
    delauto.show();
});
$('#add1').on('click', function() {
    $('#addactbtn').on('click', function() {
        let newname = $('#newacct').val();
        if (!valText(newname, ' a name for the account')) {
            return false;
        }
        let budamt  = $('#mo').val();
        if (!valAmt(budamt)) {
            return false;
        }
        let ajaxdata = {id: 'addacct', acct_name: newname, monthly: budamt}
        executeScript('../edit/saveAcctEdits.php', ajaxdata, addacct, 'home');
    });
    addacct.show();
});
$('#del1').on('click', function() {
    $('#daccbtn').on('click', function() {
        let $acct0 = $('#remacct').children();
        let acct0 = getSelect($acct0[0]);
        let ajaxdata = {id: 'acctdel', type: 'norm', acct: acct0};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, delacct, 'home');
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
        executeScript('../edit/saveAcctEdits.php', ajaxdata, moveacc, 'home');
    });
    moveacc.show();
});
$('#ren1').on('click', function() {
    $('#renbtn').on('click', function() {
        let $acct = $('#asel').children();
        let acct  = getSelect($acct[0]);
        let newname = $('#newname').val();
        if (!valText(newname, ' a new account name')) {
            return false;
        }
        let ajaxdata = {id: 'rename', acct: acct, newname: newname};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, rename, 'home');
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
$('#logout').on('click', function() {
    $.ajax({
        url: '../admin/logout.php',
        method: 'get',
        success: function() {
            window.open('../index.php', "_self");
        }
    });
});
$('#rpass').on('click', function() {
    chgpass.show();
});
$('#cpass').on('click', function() {
    let eaddr = $('#remail').val();
    if (eaddr === '') {
        alert("You must enter a valid email address");
        return false;
    }
    let adata = {email: eaddr};
    $.ajax({
        url: '../admin/sendmail.php',
        method: 'post',
        data: adata,
        dataType: 'text',
        success: function(result) {
            let sent = false;
            if (result === 'ok') {
                sent = true;
                alert('An email has been sent');
            } else if (result === 'bad') {
                alert('The email address is not valid');
            } else if (result === 'nofind') {
                alert('Your email could not be located in our database');
            }
            chgpass.hide();
            if (sent) {
                $.ajax({
                    url: '../admin/logout.php',
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
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            let msgtxt = "Error sending email:\n";
            let msg = msgtxt + textStatus + "; Error: " + errorThrown;
            alert(msg);
        }
    });
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
 * Retrieve dropdown values
 * @param {string} div The div in which the drop-down resides
 * 
 */
function getSelect(domsel) {
    let opts = domsel.options;
    let indx = domsel.selectedIndex;
    let item = domsel.options[indx].label
    return item;

}

// general purpose function to execute ajax based on input arguments
function executeScript(url, ajaxdata, modal_handle, loc) {
    $('#preloader').show();
    $.ajax({
        url: url,
        method: "POST",
        data: ajaxdata,
        dataType: "text",
        success: function(results) {
            if (results === "OK") {
                $('#preloader').hide();
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
}
/**
 * Data validation functions
 *
 */
function valAmt(amt) {
    if (Number(amt) === 'NaN' || amt === '') {
        alert("You must enter a valid sum for the Amount");
        return false;
    }
    let dot = amt.indexOf('.');
    if (dot !== -1) {
        let cents = amt.substring(dot+1);
        if (cents.length > 2) {
            alert("You have specified more than two digits for 'cents'");
            return false;
        }
    }
    return true;
}
function valPayee(payee) {
    if (payee === '') {
        alert("You have not entered a payee");
        return false;
    }
    return true;
}
function valText(item, type) {
    if (item === '') {
        alert("You must enter " + type);
        return false;
    }
    return true;
}
