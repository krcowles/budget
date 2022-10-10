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
 * Look for a non-monthlies account; if present, adjust navbar & calculate
 * expected balance.
 */
if ($('#combo_acct').length > 0) {
    $('#combo').text("Manage Non-Monthlies Account");
    var ebal = parseInt($('#combo_acct').text());
    if (ebal > 0) {
        $('.acct').each(function() {
            if ($(this).text() === 'Non-Monthlies') {
                var $ctd  = $(this).siblings().filter(".mo3");
                var cbal = parseFloat($ctd.children().eq(1).text());
                if (cbal < ebal) {
                    $(this).css('background-color', '#ffe8e6');
                    $(this).on('mouseover', function() {
                        alert('The account expected balance is: ' + ebal);
                    });
                }
                return false;
            }
        });
    }
}
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

// for admin
if ($('#mstr').text() === 'yes') {
    $('#admin').attr('type', 'button');
    // jquery can't add array of class names - because of bootstrap??
    $('#admin').addClass('btn');
    $('#admin').addClass('btn-secondary');
    $('#admin').addClass('btn-sm');
    $('#admin').css('display', 'block');
    $('#admin').off('click').on('click', function() {
        window.open('../admin/admintools.php', "_blank");
    });
}
/**
 * Set up for autopays
 */
var ap_items = new bootstrap.Modal(document.getElementById('presentap'));
// check autopayment status
var payday = [];
var paywith = []; // method of payment
var aname = []; // account name of autopay candidate
var rowno = []; // budget rowno in which AP occurs
var ap_candidates = false;

/**
 * Get autopay data, where `moday` (day-of-the-month) is not equal to 0
 * The .apday class is assigned to `moday`, and next sibling is `autopd`,
 * the latter being the month in which the last autopay was made.
 * `moday`  => integer; `autopd` => 2-char string; but all read from HTML
 * as strings by jQuery: dd, mm, yyyy are set in menus.js and are ints.
 * NOTE: budget home page is loaded with empty strings where `moday` is 0,
 * and where `autopd` is empty 
 */ 
$('.apday').each(function(indx) {
    if ($(this).text() !== "") { // this is `moday` or empty string
        // assumption: if not empty string, 'next' is also not empty
        let apday = parseInt($(this).text()); // `moday` as int
        let appd  = parseInt($(this).next().text()); // `autopd` (month) as int
        if (apday <= dd && appd !== modig) { // dd & modig are day/month ints
            $rowtds = $(this).siblings();
            // siblings do not include 'this' (moday td)
            rowno.push(indx);
            let $paywith = $rowtds.eq(5);
            let $acct = $rowtds.eq(0);
            let dueday = apday;
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
        } else if (appd === modig) {
            $(this).css('color', 'darkgray');
            $(this).prev().css('color', 'darkgray');
        }
    }
});
// user presentation of autopay candidates:
if (ap_candidates) {
    for (let j=0; j<aname.length; j++) {
        let apdata = '<tr class="lblrow"><td colspan="6">' +  // the item description ('label')
        '<span class="tdspan">' + // a span for the entire 'label' <td>
            '<span class="labelspan" style="float:left;">' +
                '<span class="aplbl">Account:</span>' +
                '<span id="acnme' + j + '">&nbsp;' + aname[j] + '</span>' +
            '</span>' + // end of labelspan
            '<span style="float:left">&nbsp;&nbsp;&nbsp;&nbsp;</span>' +
            '<span class="methodspan" style="float:left;">' +
                '<span class="aplbl">Method:</span>' +
                '<span id="acmeth' + j + '">&nbsp;' + paywith[j] + '</span>' +
            '</span>' + // end of methodspan
            '<span style="float:left">&nbsp;&nbsp;&nbsp;&nbsp;</span>' +
            '<span class="duedayspan" style="float:left;">' +
                '<span class="aplbl">Billing Date:</span>&nbsp;' + payday[j] +
            '</span>' + // end of duedayspan
        '</span>' + // end of tdspan
        '</td></tr>';
        apdata += '<tr class="btmrow"><td class="lst">Amt to Pay:</td>' +
            '<td class="lst"><input id="amt' + j + '" type="text" size="6" ' +
            '/></td><td class="lst">Payee:</td>' +
            '<td class="lst"><input id="payee' + j +'" type="text" size="8" ' +
            '/></td><td class="lst">Pay</td><td class="lst">' +
            '<input id="py' + j + '" class="paybox" type="checkbox" /></td>' +
            '</tr>';
        $('#apbody').append(apdata);
    }
    // set up actions for making autopayments
    $('#appaybtn').on('click', function() {
        // collect any checkboxes that are checked
        $('.paybox').each(function() {
            if ($(this).is(':checked')) {
                let idno = this.id;
                idno = idno.substring(2);
                let amt = $('#amt' + idno).val();
                if (!valAmt(amt, true)) {
                    return false;
                }
                let pye = $('#payee' + idno).val();
                if (!valPayee(pye)) {
                    return false;
                }
                let acct = $('#acnme' + idno).text();
                acct = acct.trim();
                let means = $('#acmeth' + idno).text();
                means = means.trim();
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
            }
        });
    });
    ap_items.show();  
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
                if ($(this).text() !== 'Non-Monthlies') {
                    $(this).css('background-color', 'gainsboro');
                }
            });
        });
        $(this).on('mouseout', function() {
            let $cells = $(this).find('td')
            $cells.each(function(i) {
                if (i < 5) {
                    if ($(this).text() !== 'Non-Monthlies') {
                        $(this).css('background-color', 'white');
                    }
                } else {
                    $(this).css('background-color', '#F8FFFA');
                }
            });
        });
    }
});

}); // end page loaded