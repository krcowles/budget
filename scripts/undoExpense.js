/**
 * @fileoverview Manage 'reverse expense' checkboxes and submit
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 * @version 3.0 Updated w/bootstrap5 menus and removed form
 */
$('#exp3').addClass('active');
var count = parseInt($('#count').text());

// collect data for posting
var ajaxdata = [];
$('input[type=checkbox]').each(function() {
    let tblid = $(this).val();
    let $tdamt = $(this).parent().next().next().next();
    let $tdacct = $tdamt.next().next();
    $(this).on('click', function() {
        if ($(this).is(':checked')) {
            let formdat = {id: tblid, amt: $tdamt.html(), acct: $tdacct.html()};
            ajaxdata.push(formdat);
        } else {
            for (let k=0; k<ajaxdata.length; k++) {
                if (ajaxdata[k].id == tblid) {
                    ajaxdata.splice(k, 1);
                    break;
                }
            }
        }
        return;
    });
});

$('#revexp').on('click', function() {
    if (count === 0) {
        alert("There are no expenses to undo");
        return;
    } else {
       if (ajaxdata.length === 0) {
            alert("There are no items checked");
            return;
        }
        let post_data = {rems: ajaxdata};
        $.ajax({
            url: "reversals.php",
            data: post_data,
            method: "post",
            success: function() {
                window.open("undoExpense.php?paid=yes", "_self");
            },
            error: function() {
                alert("Could not complete task; contact admin");
            }
        });
    }
    return;
});