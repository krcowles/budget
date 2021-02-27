/**
 * @fileoverview Manage reverse charge buttons and return button
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
var cardcnt = $('#cdcnt').text();

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
$('form').on('submit', function() {
    if (cardcnt === '0') {
        alert("There are no card charges to reverse");
        return false;
    } else {
        let nochecks = true
        $('input[type=checkbox]').each(function() {
            if ($(this).is(':checked')) {
                nochecks = false;
                return;
            }
        });
        if (nochecks) {
            alert("There are no items checked");
            return false;
        }
    }
});
$('#return').on('click', function(ev) {
    ev.preventDefault();
    let bud = "../main/displayBudget.php";
    window.open(bud, "_self");
});