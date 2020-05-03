var user = $('input[name=user]').attr('value');
$('input[type=checkbox]').each(function() {
    $(this).on('change', function() {
        let item = $(this).attr('value');
        let divid = 'div[id=' + item + ']';
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
    let bud = "../main/displayBudget.php?user=" + user;
    window.open(bud, "_self");
});