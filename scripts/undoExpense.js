/**
 * @fileoverview Manage 'reverse expense' checkboxes and return button
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
$('input[type=checkbox]').each(function() {
    $(this).on('change', function() {
        let item = $(this).attr('value');
        let divid = 'div[id=d' + item + ']';
        if ($(this).is(':checked')) {
            $(divid).children().each(function() {
                $(this).css('background-color', 'blanchedalmond');
            });
        } else {
            $(divid).children().each(function() {
                $(this).css('background-color', 'white');
            });
        }
    });
});
$('#return').on('click', function(ev) {
    ev.preventDefault();
    let bud = "../main/displayBudget.php"
    window.open(bud, "_self");
});