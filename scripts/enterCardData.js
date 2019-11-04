$(function() {

// buttons
$('#save').on('click', function() {
    $('#form').submit();
});
$('#return').on('click', function(ev) {
    ev.preventDefault();
    window.open("../main/budget.php", "_self");
});

if ($('#olddat').text() !== '0') {
    $('#existing').css('display', 'block');
}

// modal for editing old data:
var $old_dat = $('input[id^=ed');
$old_dat.each(function() {
    $(this).change(function() {
        var $chkbox = $(this);
        if ($chkbox.prop('checked')) {
            var inpid = this.id;
            var card_no = inpid.charAt(2); // always 1 digit
            var itemloc = inpid.substr(7);
            var idinfo = {cdno: card_no, itemno: itemloc};
            var $cbtd = $(this).parent();
            var $tds = $cbtd.siblings();
            $tds.each(function() {
                $(this).css('background-color', 'lightgoldenrodyellow');
            });
            var $affected_row = $cbtd.parent();
            var $linedat = $cbtd.siblings();
            var inputs = [];
            inputs.push($linedat.eq(0).text());
            inputs.push($linedat.eq(1).text());
            inputs.push($linedat.eq(2).text());
            inputs.push($linedat.eq(3).text());
            var $popup = $('#item_modal').detach();
            // modal will remove html inside the div; $popup can be re-appended
            var deferred = new $.Deferred();
            modal.open({id:'edit_chg', width: '360px', height: '220px',
                content: $popup, ivals: inputs,
                chgitem: $affected_row, chgid: idinfo, def: deferred});
            $.when(deferred).then(function() {
                $popup.appendTo('body');
                location.reload();
            });
        }
    });
});

});