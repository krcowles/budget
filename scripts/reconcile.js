$(function() {

// id the options present in the <select> box
var opts = [];
var selbox = document.getElementById('sel');
for (var j=0; j<selbox.options.length; j++) {
    opts[j] = selbox.options[j].value;
}
var card_cnt = opts.length;
var display_index;
// change the display to match the default card selected
$('#list0').css('display', 'block');
$('#sel').change(function() {
    $('div[id^=list]').each(function() {
        $(this).css('display', 'none');
    });
    card2use = $('#sel option:selected').val();
    var id;
    for (var k=0; k<card_cnt; k++) {
        if (card2use == opts[k]) {
            display_index = k;
            id = '#list' + k;
            break;
        }
    }
    $(id).css('display', 'block');
});
// checkbox row highlighting:
$chkbox = $('input:checkbox[id^=card]');
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
    window.open('../main/budget.php', '_self');
});

});
