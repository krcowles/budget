/**
 * @fileoverview The main budget display, which automatically updates with
 * potential month-rollover, and manages autopay activity
 * 
 * @author Ken Cowles
 * @version 3.0 Bootstrap navbar
 */
$(function() { 

// Modal handles:
var apitems = new bootstrap.Modal(document.getElementById('apmodal'));
var expitem = new bootstrap.Modal(document.getElementById('expmodal'));
var depinc  = new bootstrap.Modal(document.getElementById('incmodal'));
var onetdep = new bootstrap.Modal(document.getElementById('othrdeps'));
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
 * Set up for autopays
 */
// get date info to check for upcoming or past due autopays
var today = new Date();
var dd = parseInt(String(today.getDate()).padStart(2, '0'));
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January was otherwise 0!
var yyyy = parseInt(today.getFullYear());
// for admin
if ($('#mstr').text() === 'yes') {
    $('#admin').css('display', 'block');
    $('#admin').on('click', function() {
        window.open('../admin/admintools.php', "_blank");
    });
}
// check autopayment status
var payday = [];
var paywith = []; // method of payment
var aname = []; // account name of autopay candidate
var rowno = []; // budget rowno in which AP occurs
var ap_candidates = false;
// get all autopays having dates; find those that are due
$('.apday').each(function(indx) {
    if ($(this).text() !== "") {
        let apday = parseInt($(this).text());
        if (apday <= dd) {
            $rowtds = $(this).siblings();
            let pd = $rowtds.eq(6).text().trim();
            if (pd !== mm) {
                rowno.push(indx);
                let $paywith = $rowtds.eq(5);
                let $acct = $rowtds.eq(0);
                let dueday = parseInt($(this).text());
                let ordinal;
                switch (dueday) {
                    case 1:
                        ordinal = 'st';
                        break;
                    case 2: 
                        ordinal = 'nd';
                        break;
                    case 3:
                        ordinal = 'rd';
                        break;
                    default:
                        ordinal = 'th';
                }
                dueday += ordinal;
                payday.push(dueday);
                paywith.push($paywith.text().trim());
                aname.push($acct.text());
                ap_candidates = true;
            } else {
                $(this).css('color', 'darkgray');
                $(this).prev().css('color', 'darkgray');
            }
        }
    }
});
// user presentation of autopay candidates:
if (ap_candidates) {
    //  create a table of due autopays
    let $userlist = $('<table id="modal_table"><tbody></tbody></table>');
    // charges.css takes precedence on td, so using styled elements here
    for (let j=0; j<aname.length; j++) {
        let apdata = '<tr><td colspan="6" style="color:darkgreen;">' +
            '<span id="acnme' + j + '">Account: ' + aname[j] + '</span><span ' +
            'style="float:left">&nbsp;/&nbsp;</span>' +
            '<span id="acmeth' + j + '" style="float:left;">Pay Using: ' +
            paywith[j] + '</span></div></td></tr>';
        apdata += '<tr><td style="vertical-align:middle;">Amt:</td>' +
            '<td style="vertical-align:middle;"><input id="amt' + j + '" type="text" /></td>' +
            '<td style="vertical-align:middle;">To: </td>' +
            '<td style="vertical-align:middle;"><input id="payee' + j + '" type="text" /></td>' +
            '<td style="vertical-align:middle;">Due: ' + payday[j] + '</td>' +
            '<td style="vertical-align:middle;">Pay It&nbsp;&nbsp;' +
            '<input id="py' + j + '" class="paybox" type="checkbox" /></td></tr>';
        $userlist.append(apdata);
    }
    $('#ap').append($userlist);
    // set up actions for making autopayments
    $('.paybox').each(function() {
        let payid = '#' + this.id;
        $('body').on('click', payid, function() {
            let idno = this.id;
            idno = idno.substring(2);
            let amt = $('#amt' + idno).val()
            let pye = $('#payee' + idno).val();
            let acct = $('#acnme' + idno).text();
            acct = acct.substring(9);
            let means = $('#acmeth' + idno).text();
            means = means.substring(11);
            if (!valAmt(amt)) {
                return false;
            }
            if (!valPayee(pye)) {
                return false;
            }
            let ajaxdata = {acct: acct, amt: amt, payee: pye, method: means};
            $.ajax({
                url: '../utilities/makeAutopayment.php',
                method: 'post',
                data: ajaxdata,
                dataType: 'text',
                success: function(result) {
                    if (result === 'OK') {
                        window.open("../index.php", "_self");
                    } else {
                        alert("A problem was encountered with autopayment\n" +
                            "Contact support");
                    }
                },
                error: function() {
                    alert("Failed to make autopayment: contact support");
                }
            });
        });
    });
    apitems.show();  
}

/**
 * All other modal operation
 */
$('#chgexp').on('click', function() {
    // triggering twice, don't know why:
    $('#pebtn').unbind('click').bind('click', function() {
    //$('body').on('click', '#pebtn', function() {
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
        let expdone = $.Deferred();
        executeScript('../edit/saveAcctEdits.php', ajaxdata, expitem, expdone);
        $.when( expdone ).then( function() {
            return;
        });
    });
    expitem.show();
});
$('#reginc').on('click', function() {
    $('#incbtn').on('click', function() {
        let reg = $('#incdep').val();
        if (!valAmt(reg)) {
            return false;
        }
        let regdef = $.Deferred();
        let ajaxdata = {id: 'income', funds: reg};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, depinc, regdef);
        $.when( regdef ).then(function() {
            return;
        });
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
        let otdef = $.Deferred();
        let ajaxdata = {id: 'otdeposit', newfunds: funds, note: otmemo};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, onetdep, otdef);
        $.when( otdef ).then( function() {
            return;
        });
    });
    onetdep.show();
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
        let xfrdef = $.Deferred();
        let ajaxdata = {id: 'xfr', from: xfrom, to: xfrto, sum: amt};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, xfrfund, xfrdef);
        $.when( xfrdef ).then(function() {
            return;
        });
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
        let acddef = $.Deferred();
        executeScript('../edit/saveAcctEdits.php', ajaxdata, addcard, acddef);
        $.when( acddef ).then(function() {
            return;
        });

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
        let dcdef = $.Deferred();
        executeScript('../edit/saveAcctEdits.php', ajaxdata, delcard, dcdef);
        $.when( dcdef ).then(function() {
            return;
        });
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
        let addapdef = $.Deferred();
        let ajaxdata = {id: 'apset', acct: apadder, method: card, day: day};
        executeScript('../edit/saveAcctEdits.php', ajaxdata, autopay, addapdef);
        $.when( addapdef ).then(function() {
            return;
        });
    });
    autopay.show();
});
$('#rmap').on('click', function() {
    $('#delapbtn').on('click', function() {
        let $delap = $('#delapacct').children();
        let delapp = getSelect($delap[0]);
        let ajaxdata = {id: 'delapay', acct: delapp};
        let dapdef = $.Deferred();
        executeScript('../edit/saveAcctEdits.php', ajaxdata, delauto, dapdef);
        $.when( dapdef ).then(function() {
            return;
        });
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
        let aacctdef = $.Deferred();
        executeScript('../edit/saveAcctEdits.php', ajaxdata, addacct, aacctdef);
        $.when( aacctdef ).then(function() {
            return;
        });
    });
    addacct.show();
});
$('#del1').on('click', function() {
    $('#daccbtn').on('click', function() {
        let $acct0 = $('#remacct').children();
        let acct0 = getSelect($acct0[0]);
        let ajaxdata = {id: 'acctdel', acct: acct0};
        let d1def = $.Deferred();
        executeScript('../edit/saveAcctEdits.php', ajaxdata, delacct, d1def);
        $.when( d1def ).then(function() {
            return;
        });
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
        let mvdef = $.Deferred();
        executeScript('../edit/saveAcctEdits.php', ajaxdata, moveacc, mvdef);
        $.when( mvdef ).then(function() {
            return;
        });
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
        let rendef = $.Deferred();
        executeScript('../edit/saveAcctEdits.php', ajaxdata, rename, rendef);
        $.when( rendef ).then(function() {
            return;
        });
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
function executeScript(url, ajaxdata, modal_handle, deferred) {
    $('#preloader').show();
    $.ajax({
        url: url,
        method: "POST",
        data: ajaxdata,
        dataType: "text",
        success: function(results) {
            if (results === "OK") {
                deferred.resolve();
                location.reload();
                $('#preloader').hide();
            } else {
                alert("Problem encountered; Operation did not complete");
                modal_handle.hide();
                deferred.resolve();
                $('#preloader').hide();
            }
           
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('#preloader').hide();
            msg = msgtxt + ":\n" + textStatus + "; Error: " + errorThrown;
            alert(msg);
            modal_handle.hide();
            deferred.reject();
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

}); // end page loaded