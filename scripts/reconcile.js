/**
 * @fileoverview Provide a table of expenses charged to the 
 * subject Credit Card; Mark items to be paid and process them
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
$(function() {

// checkbox row highlighting:
$chkbox = $('input:checkbox[id^=chg]');
$chkbox.each(function() {
    $(this).click(function() {
        if ($(this).is(':checked')) {
            var $csibs = $(this).parent().siblings();
            $csibs.each(function() {
                $(this).css('background-color', 'blanchedalmond');
            });
        } else {
            var $usibs = $(this).parent().siblings();
            $usibs.each(function() {
                $(this).css('background-color', 'white');
            });
        }
    });
});

$('#rtb').on('click', function(ev) {
    ev.preventDefault();
    var backpg = "../main/displayBudget.php";
    window.open(backpg, '_self');
});

});
