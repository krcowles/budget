/**
 * @fileoverview Present a table of expenses charged to the 
 * subject Credit Card; User marks items to be paid and submits them.
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 * @version 2.1 Changed styling - added navpanel to page
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
$('#reconcile_chgs').on('click', function() {
    $('#form').trigger('submit');
});

});
