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

var user = $('#user').text();
$('#rtb').on('click', function(ev) {
    ev.preventDefault();
    var backpg = "../main/displayBudget.php?user=" + user;
    window.open(backpg, '_self');
});

});
