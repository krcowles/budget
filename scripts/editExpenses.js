/**
 * @fileoverview This script manages buttons and form submittal
 * for the editExpenses.php script
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 * @version 2.1 Changed page styling & added navpanel
 */
$( function() {

$( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
    });
var $amount = $('.amt');
scaleTwoNumber($amount);

// get rows for data initialization
$('table tbody tr').each(function(indx) {
    let $row_tds = $(this).children(); // all <td>'s
    let $methbox = $row_tds.children().eq(0);
    let paymeth = $row_tds.eq(9).html();
    $methbox.val(paymeth);
    if (paymeth === 'Debit') {
        let cdname = $row_tds.eq(10).text();
        let $cdbox = $row_tds.children().eq(1);
        $cdbox.val(cdname);
    }
    let acct = $row_tds.eq(8).children().eq(0).val();
    let $acctbox = $row_tds.eq(5).children().eq(0);
    $acctbox.val(acct);
});

// Manage changes to Pay Method/Card Name
$('.meths').on('change', function() {
    let $cdnamebx = $(this).parent().siblings().eq(0).children().eq(0);
    if ($(this).val() === 'Check') { 
        $cdnamebx.val('Check');
    } else if (this.options.length === 2) {
        let cardname = $cdnamebx.get(0).options[1].innerHTML;
        $cdnamebx.val(cardname);
    } else {
        alert ("Please select a Card Name");
    }
});

$('#save').on('click', function(ev) {
    ev.preventDefault();
    var proceed = true;
    $('.meths').each(function() {
        if ($(this).val() === 'Debit') {
            let $corresbox = $(this).parent().siblings().eq(0).children().eq(0);
            if ($corresbox.val() === 'Check') {
                alert("Debit Payments must not have a Card Name of 'N/A'");
                proceed = false;
                return;
            }
        }   
    });
    $('.fullsel').each(function() {
        if ($(this).val() === '') {
            ev.preventDefault();
            alert("All 'Deducted From' select boxes must have a current\n" +
                "account selected");
            proceed = false;
            return;
        }
    });
    if (proceed) {
        $('#form').trigger('submit');
    } else {
        return;
    }
});

});
