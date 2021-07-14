/**
 * @fileoverview This script manages page appearance as well as
 * invocation of available 'Manage Expenses' sub-menu functions.
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 * @version 3.0 Added bootstrap menu at top, revised action invocation
 * @version 3.1 Modified window.open's to target self
 */
$(function() {

$('#exp1').addClass('active');
// remove cancel button and save for appending during certain user actions
var canbtn = $('#canc').detach();
// reset all checkboxes:
$('.mvtocr').prop('checked', false);
$('.mvtodr').prop('checked', false);
$('input[id^=cr]').prop('checked', false);
$('input[id^=dr]').prop('checked', false);
$('input[id^=to]').prop('checked', false);

/**
 * This function appends the cancel button to the correct location on the page
 * @param {string} xfrtype The jquery id of the <span> element to append
 * @return {null}
 */
function enableCancel(location) {
    $(location).append(canbtn);
    $('#canc').on('click', function() {
        // don't reload() - there may be a query string attached
        window.open("viewCharges.php", "_self"); 
    });
    return;
}
/**
 * When a cancel button is already being displayed, a transaction is underway.
 * If the user switches to another type of transaction without completing the 
 * current one, the page is reloaded but with information as to the next button
 * to be triggered. If no cancel button is in evidence, the trigger fct continues.
 * @return {null}
 */
 function relocateCancel(newbtn) {
    let retrigger = "viewCharges.php?click=" + newbtn;
    let $e2cbtn = $('#mve2c').find('button');
    let $c2cbtn = $('#mvc2c').find('button');
    let $c2ebtn = $('#mvc2e').find('button');
    if ($e2cbtn.length !== 0 || $c2cbtn.length !==0 || $c2ebtn.length !== 0) {
        window.open(retrigger, "_self");
    }
    return;
}

/**
 * bootstrap button invocations:
 */ 
// edit credit charges
$('#edcr').on('click', function() {
    window.open('../edit/editCreditCharges.php', '_self');
});
// edit expenses
$('#edexp').on('click', function() {
    alert("NOTE: You will be able to modify paid expense data from the last\n" +
        "30 days, including checks and debit card charges. Any changes\n" +
        "in the $ amounts will be reflected in the associated account(s)");
    window.open('../edit/editExpenses.php', '_self');
});

// move expense to credit 
$('#e2c').on('click', function() {
    relocateCancel('e2c');
    enableCancel('#mve2c');
    var selected = false;
    var msg = "NOTE: Moving a paid (deducted) expense to\na credit card charge" +
        " will increase\nyour checkbook balance";
    var ans = confirm(msg);
    if (ans) {
        var from;
        var to;
        $('#canc').on('click', function() {
            $('.hddr').removeClass('blinking');
            $('.mvdr').css('display', 'none');
            $('mvtocr').removeClass('blinking');
            $('mvtocr').css('display', 'none');
        });
        $('.mvdr').css('display', 'inline');
        $('.hddr').addClass('blinking');
        $('input[id^=dr').on('click', function() {
            if ($(this).prop('checked') && !selected) {
                selected = true;
                $('.hddr').removeClass('blinking');
                var frmid = this.id;
                from = frmid.substr(2);
                $('.mvtocr').css('display', 'inline');
                $('.mvtocr').addClass('blinking');
                $('.mvtocr input').on('click', function() {
                    $('.mvtocr').removeClass('blinking');
                    var toid = $(this).attr('id');
                    to = toid.substr(4);
                    var query = '../edit/moveExp.php?type=e2c&frm=' + from + "&to=" + to;
                    window.open(query, "_self");
                });
            } else if ($(this).prop('checked') && selected) {
                alert("Only one item may be selected: to change,\nUncheck the " +
                    "current item, then check the one you want");
                $(this).prop('checked', false);
            } else if (!$(this).prop('checked')) {
                // undo display w/blink
                $('.mvtocr').removeClass('blinking');
                $('.mvtocr').css('display', 'none');
                selected = false;
            }
        });
    } else {
        $('#canc').trigger('click');
    }
});
// move from one credit card to another
$('#c2c').on('click', function() {
    relocateCancel('c2c');
    enableCancel('#mvc2c');
    var selected = false;
    var from;
    var to;
    $('.mvcr').css('display', 'inline');
    $('.hdcr').addClass('blinking');
    $('input[id^=cr').on('click', function() {
        if ($(this).prop('checked') && !selected) {
            selected = true;
            $('.hdcr').removeClass('blinking');
            var fromid = this.id;
            from = fromid.substr(2);
            // hide only the cards in groups not selected
            var $td = $(this).parent();
            var $tbl = $td.parent().parent().parent();
            $tbl[0].id = 'selectedtbl';
            $('.mvcr').each(function() {
                // find the <th> element with class 'hdcr'
                var $thistbl = $(this).parent().parent().parent();
                if ($(this).hasClass('hdcr') 
                        && $thistbl.attr('id') !== 'selectedtbl') {
                    $(this).css('display', 'none');
                    var $tds = $thistbl.find('tbody tr td.mvcr');
                    //.css('display', 'none');
                    $tds.css('display', 'none');
                }
            });
            // turn on other cc selectors
            $('table').each(function() {
                if ($(this).attr('id') !== 'selectedtbl') {
                    var $spanto = $(this).prev().prev();
                    if ($spanto.hasClass('mvtocr')) {
                        $spanto.css('display', 'inline');
                        $spanto.addClass('blinking');
                    }
                }
            });
            $('input[id^=tocr]').on('click', function() {
                var toid = this.id;
                to = toid.substr(4);
                var query = "../edit/moveExp.php?type=c2c&frm=" + from + "&to=" + to;
                window.open(query, "_self");
            });
        } else if ($(this).prop('checked') && selected) {
            alert("Only one item may be selected: to change,\nUncheck the " +
                "current item, then check the one you want");
            $(this).prop('checked', false);
        } else if (!$(this).prop('checked')) {
            // undo display w/blink
            selected = false;
        }
    });
});
// move a credit charge to an expense paid item
$('#c2e').on('click', function() {
    relocateCancel('c2e');
    enableCancel('#mvc2e');
    var selected = false;
    var msg = "NOTE: Moving a credit charge to a paid expense\n" +
        "will decrease your checkbook balance";
    var ans = confirm(msg);
    if (ans) {
        var from;
        var to;
        $('.mvcr').css('display', 'inline');
        $('.hdcr').addClass('blinking');
        $('input[id^=cr]').on('click', function() {
            if ($(this).prop('checked') && !selected) {
                selected = true;
                $('.hdcr').removeClass('blinking');
                var fromid = this.id;
                from = fromid.substr(2);
                $('.mvtodr').css('display', 'inline');
                $('.mvtodr').addClass('blinking');
                $('input[id^=todr]').on('click', function() {
                    $('.mvtodr').removeClass('blinking');
                    var toid = this.id;
                    to = toid.substr(4);
                    var query = '../edit/moveExp.php?type=c2e&frm=' + from + "&to=" + to;
                    window.open(query, "_self");
                });
            } else if ($(this).prop('checked') && selected) {
                alert("Only one item may be selected: to change,\nUncheck the " +
                    "current item, then check the one you want");
                $(this).prop('checked', false);
            } else if (!$(this).prop('checked')) {
                 // undo display w/blink
                 $('.mvtodr').removeClass('blinking');
                 $('.mvtodr').css('display', 'none');
                 selected = false;
            }
        });
    }
});
// see if the page needs to trigger an action via php
// this code must be placed AFTER the click definitions!
let act = $('#btn2click').text();
if (act !== 'none') {
    $('#' + act).trigger('click');
    // NOTE: When using '#' in a query string, the var will return empty!
}

});