/**
 * @fileoverview Present a table of expenses charged to the 
 * subject Credit Card; User marks items to be paid and submits them.
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 * @version 2.1 Changed styling - added navpanel to page
 * @version 2.2 Added running tab of reconciled charges to page
 */
$(function() {

/**
 * Get the current sum of unchecked boxes in the reconcile table
 * 
 * @return {null}
 */
function summer() {
    $rows.each(function() {
        let $chkbox = $(this).find('td').eq(4).children().eq(0);
        let $amt = $(this).find('td').eq(1);
        let amt = parseFloat($amt.text());
        amt = Math.round(100 * amt)/100;
        if ($chkbox.prop('checked')) {
            curr_rec += amt;
        } else {
            curr_not += amt;
        }
    });
    $('#sumtxt').text(curr_rec.toFixed(2));
    $('#rectxt').text(curr_not.toFixed(2));
    return;
}

/**
 * Apply coloring to checked item and sum the unchecked items
 * 
 * @return {null}
 */
function colorizeAndSum() {
    $chkbox.each(function() {
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
        summer();
        curr_rec = 0;
        curr_not = 0;
    });
}

// Position the sum data
var tbl_loc   = $('.sortable').offset();
var top_align = tbl_loc.top;
var left      = ($('.sortable').width() + tbl_loc.left + 40) + 'px';
var curr_rec  = 0;
$('#rsum').css({
    position: "fixed",
    top: top_align,
    left: left
});

/**
 * On page load / reload
 */
$chkbox = $('input:checkbox[id^=chg]');
$rec_tbl = $('table.sortable tbody');
$rows = $rec_tbl.find('tr');
var curr_rec = 0; // initialize sum text
var curr_not = 0;
colorizeAndSum();

$chkbox.on('click', function() {
    colorizeAndSum();
});

$('#reconcile_chgs').on('click', function() {
    $('#form').trigger('submit');
});

});
