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
    // initialize acct drop-downs for old data:
    var boxvals = [];
    $('span[id^=oc]').each(function() {
        boxvals.push($(this).text());
    });
    $('#existing .acctsel').each(function(indx) {
        $(this).val(boxvals[indx]);
        var newid = 'selbox' + indx;
        $(this).prop('id', newid);
    });
}
var $modselect = $('#item_edit table tr').eq(0).children().eq(1).children().eq(0);
$modselect.prop('id', 'modsel');
$('#item_edit table tr').eq(0).children().eq(1).children().eq(0).prop('id', 'modsel');
// call modal to edit old (already existing) data:
$('input[id^=ed').each(function(i) {  // every edit checkbox
    $(this).change(function() {
        if ($(this).prop('checked')) {
            var inpid = this.id;
            var card_no = inpid.charAt(2); // always 1 digit (only 4 cards for now)
            var itemloc = inpid.substr(7);
            var idinfo = {cdno: card_no, itemno: itemloc};
            var $cbtd = $(this).parent(); // the <td> in which checkbox resides
            var $tds = $cbtd.siblings(); // all <td>s minus checkbox <td>, aka $(this)
            $tds.each(function() {
                $(this).css('background-color', 'floralwhite');
            });
            $cbtd.css('background-color', 'floralwhite');
            var $affected_row = $cbtd.parent();
            // get the selected text from sib0
            var cbid = '#selbox' + i + ' option:selected';
            var editacct = $(cbid).val();
            var inputs = [];
            inputs.push(editacct);
            inputs.push($tds.eq(1).text());
            inputs.push($tds.eq(2).text());
            inputs.push($tds.eq(3).text());
            var $popup = $('#item_edit').detach();
            // modal will remove html inside the div; $popup can be re-appended
            var deferred = new $.Deferred();
            modal.open({id:'edit_chg', width: '360px', height: '280px',
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