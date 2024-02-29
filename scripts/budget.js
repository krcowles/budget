/**
 * @fileoverview The main budget display, which automatically updates with
 * potential month-rollover, and manages autopay activity
 * 
 * @author Ken Cowles
 * @version 3.0 Bootstrap navbar
 * @version 4.0 Separated navbar operation into menus.js; added row highlighting
 * @version 5.0 Added autopay for Non-Monthlies acccount
 */
$(function() { 

$('#bp').css('display', 'none'); // not needed on home (budget) page
/**
 * Look for a non-monthlies account; if present, adjust navbar text & calculate
 * expected balance.
 */
if ($('#combo_acct').length > 0) {
    // set navbar menu item (default is "Add Non-Monthlies Account"):
    $('#combo').text("Manage Non-Monthlies Account");
    let actual = parseFloat($('#combo_acct').text());
    let expected = parseFloat($('#expected_sum').text());
    let nonm_note = $('.acct:contains("Non-Monthlies")');
    nonm_note.attr('id', 'nmacct');
    let nmpos = $(nonm_note).offset();
    let note_left = nmpos.left + 120;
    let note_top  = nmpos.top + 2;
    let note = '<div id="nmnote" style="position:absolute;z-index:100;' +
        'top:' + note_top + 'px;left:' + note_left + 'px;"' +
        '>Click for status</div>';
    $('body').append(note);
    $(nonm_note).hover(
        function() {
            $('#nmnote').css('display', 'block');
        },
        function() {
            $('#nmnote').css('display', 'none');
        }
    );
    $(nonm_note).on('click', function() {
        alert("Expect " + expected + "; Available: " + actual);
    });
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

$('#medpg').on('click', function() {
    window.open("../medrefs/index.php");
});
// for admin
if ($('#mstr').text() === 'yes') {
    $('#admin').css('display', 'block');
    $('#admin').off('click').on('click', function() {
        window.open('../admin/admintools.php', "_blank");
    });
}
/**
 * Set up for autopays - budget page only; for non-monthlies, see #combo_acct
 */
var ap_items = new bootstrap.Modal(document.getElementById('presentap'));
// check autopayment status
var payday  = [];
var paywith = [];  // method of payment
var aname   = [];  // account name of autopay candidate
var ptype   = [];  // whether a budget or a non-monthlies acct autopay
var ap_candidates = false;

/**
 * All data is extracted in budget_setup.php and presented to the page as
 * script variables.
 *  1. For budget page autopays, get autopay data, where `moday`
 *     (day-of-the-month) is not equal to 0.the .apday class is assigned to
 *     `moday`. The next sibling is `autopd`, that being the month in which
 *     the last autopay was made. Here `moday`  => integer;
 *     `autopd` => 2-char string; The vars dd, mm, yyyy are set in menus.js
 *     and are ints.
 *     NOTE: the budget home page is loaded with empty strings where
 *     `moday` is 0, and where `autopd` is empty 
 *  2. For the Non-Monthlies account, if present, extract qualified autopays
 *     from the script variables assigned in budget_setup.php.
 *  3. NOTE: var modig is the current month (1-based!) defined in menus.js
 */ 
$('.apday').each(function(indx) {
    if ($(this).text() !== "") { // this is `moday` or empty string
        // assumption: if not empty string, 'next' is also not empty
        let apday = parseInt($(this).text()); // `moday` as int
        let appd  = parseInt($(this).next().text()); // `autopd` (month) as int
        if (apday <= dd && appd !== modig) { // dd & modig are day/month ints
            $rowtds = $(this).siblings();
            // siblings do not include 'this' (moday td)
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
            ptype.push('bud');
            ap_candidates = true;
        } else if (appd === modig) {
            $(this).css('color', 'darkgray');
            $(this).prev().css('color', 'darkgray');
        }
    }
});
/**
 * If there is a 'Non-Monthlies' account, and it has specified UNPAID 
 * autopays, add them to the arrays created above for posting.
 * NOTE: "nonm_*" vars are defined in displayBudget.php; nonm_apnext
 * values are  0-based;
 */
if ($('#combo_acct').length > 0) {
    for (let k=0; k<nonm_apacct.length; k++) {
        if (modig-1 > nonm_apnext[k]
        || (nonm_apnext[k] === (modig-1) && dd >= nonm_apdays[k])) {
                payday.push(nonm_apdays[k]);
                paywith.push(nonm_aptype[k]);
                aname.push(nonm_apacct[k]);
                ptype.push('non');
                ap_candidates = true;
        }
    }
}

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
            '<td id="ptype' + j + '" style="display:none;">' + ptype[j] + '</td></tr>';
        $('#apbody').append(apdata);
    }
    // set up actions for making autopayments
    $('#appaybtn').on('click', function() {
        // collect any checkboxes that are checked
        var noOfChecked = 0;
        $('.paybox').each(function() {
            if ($(this).is(':checked')) {
                noOfChecked++;
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
                let acctype = $('#ptype' + idno).text();
                let ajaxdata = {acct: acct, amt: amt, payee: pye, 
                    method: means, acctype: acctype};
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
        if (noOfChecked === 0) {
            alert("You have not checked any autopays for payment");
        }
    });
    $('#payap').on('click', function() {
        alert("No action has been taken");
    });
    ap_items.show();  
}

/**
 * CSS doesn't recognize a tr:hover for this table, so...
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
                }  else {
                    $(this).css('background-color', '#F8FFFA');
                }
            });
        });
    }
});
}); // end page loaded
