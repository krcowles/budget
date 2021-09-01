/**
 * @fileoverview The main budget display, which automatically updates with
 * potential month-rollover, and manages autopay activity
 * 
 * @author Ken Cowles
 * @version 3.0 Bootstrap navbar
 * @version 4.0 Spearated navbar operation into menus.js; added row highlighting
 */
$(function() { 

$('#bp').css('display', 'none'); // not needed on home (budget) page
/**
 * If an income deferral was recorded earlier...
 */
var currmo = $('#currmo').text();
var nxtmo  = $('#nextmo').text();
$('#defermo').text(nxtmo);
var trigger_mo = $('#deferral').text();
var def_amt = $('#defamt').text();
if (trigger_mo === currmo) {
    // 1st, Distribute the Deferred Income:
    let url = '../edit/saveAcctEdits.php';
    let ajaxdata = {id: 'income', funds: def_amt};
    $.ajax({
        url: url,
        method: 'post',
        data: ajaxdata,
        dataType: 'text',
        success: function(results) {
            if (results === 'OK') {
                // Next, delete the 'Deferred Income' account
                let deldata = {id: 'acctdel', type: 'def', acct: 'Deferred Income'};
                $.ajax({
                    url: url,
                    data: deldata,
                    method: "post",
                    dataType: "text",
                    success: function(result) {
                        if (result !== 'OK') {
                            alert("Could not delete 'Deferred Income' acct");
                        } else {
                            location.reload();
                        }
                    }, 
                    error: function(jqXHR, textStatus, errorThrown) {
                        let msgtxt = "Error deleting 'Deferred Income'\n";
                        let msg = msgtxt + ":\n" + textStatus + "; Error: " + errorThrown;
                        alert(msg);
                    }  
                });
            } else {
                alert("Problem trying to distribute deferred income\n" +
                    "Contact admin");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            let msgtxt = "Error distributing 'Deferred Income':\n";
            let msg = msgtxt + textStatus + "; Error: " + errorThrown;
            alert(msg);
        }
    });
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
    $('#admin').attr('type', 'button');
    // jquery can't add array of class names - because of bootstrap??
    $('#admin').addClass('btn');
    $('#admin').addClass('btn-secondary');
    $('#admin').addClass('btn-sm');
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
                        location.reload();
                    } else {
                        alert("A problem was encountered with autopayment\n" +
                            "Contact support");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    let msgtxt = "Error trying to make autopayment:\n";
                    let msg = msgtxt + textStatus + "; Error: " + errorThrown;
                    alert(msg);
                }
            });
        });
    });
    apitems.show();  
}

/**
 * CSS simply won't recognize a tr:hover for this table, so...
 */
let $allrows = $('#roll3').find('tbody tr');
$allrows.each(function() {
    let c1 = $(this).children().eq(0).text();
    if (c1 !== 'Temporary Accounts' && c1 !== 'Credit Cards' &&
            c1 !== 'Checkbook Balance') {
        $(this).on('mouseover', function() {
            let $cells = $(this).find('td')
            $cells.each(function() {
                $(this).css('background-color', 'gainsboro');
            });
        });
        $(this).on('mouseout', function() {
            let $cells = $(this).find('td')
            $cells.each(function(i) {
                if (i < 5) {
                    $(this).css('background-color', 'white');
                } else {
                    $(this).css('background-color', '#F8FFFA');
                }
            });
        });
    }
});

}); // end page loaded