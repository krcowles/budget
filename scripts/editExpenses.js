/**
 * @fileoverview This script manages buttons and form submittal
 * for the editExpenses.php script
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
$(function() {

$('#return').on('click', function(ev) {
    ev.preventDefault();
    let dpg = "../main/displayBudget.php"; 
    window.open(dpg, "_self");
});
$('#viewer').on('click', function(ev) {
    ev.preventDefault();
    let vpg = "../utilities/viewCharges.php";
    window.open(vpg, "_self");
});
$(function () {
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });
    var $amount = $('.amt');
    scaleTwoNumber($amount);
});

// initialize select boxes:
var $accts = $('span[id^=acct]');
var init_acct = [];
for (var k=0; k<$accts.length; k++) {
    init_acct.push($accts[k].textContent);
}
var $tbldat = $('table tbody tr');
$tbldat.each(function(indx) {
    var $selbox = $(this).children().eq(5).children().eq(0);
    $selbox.val(init_acct[indx]);
});

$('#form').submit(function(ev) {
    $('.fullsel').each(function() {
        if ($(this).val() === '') {
            ev.preventDefault();
            alert("All 'Deducted From' select boxes must have a current\n" +
                "account selected");
            return true;
        }
    });
});

});