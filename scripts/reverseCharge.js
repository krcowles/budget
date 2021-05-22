/**
 * @fileoverview Manage reverse charge buttons and return button
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
var cardcnt = $('#cdcnt').text();
$('#exp2').addClass('active');

// collect data for posting
var ajaxdata = [];
$('input[type=checkbox]').each(function() {
    let tblid = $(this).val();
    let $chkbxtd = $(this).parent();
    let $amttd = $chkbxtd.next();
    let $datetd = $amttd.next();
    let $accttd = $datetd.next();
    $(this).on('click', function() {
        if ($(this).is(':checked')) {
            let formdat = {id: tblid, amt: $amttd.html(), acct: $accttd.html()};
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

$('#reverse').on('click', function() {
    if (cardcnt === '0') {
        alert("There are no card charges to reverse");
        return false;
    } else {
        if (ajaxdata.length === 0) {
            alert("There are no items checked");
        }
        let post_data = {rems: ajaxdata};
        $.ajax({
            url: "doReverse.php",
            data: post_data,
            method: "post",
            success: function() {
                window.open("reverseCharge.php?paid=yes", "_self");
            },
            error: function() {
                alert("Could not complete task; contact admin");
            }
        });
    }
    return;
});
