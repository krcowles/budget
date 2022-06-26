/**
 * @fileoverview Present a table of expenses charged to the 
 * subject Credit Card; User marks items to be paid and submits them.
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 * @version 2.1 Changed styling - added navpanel to page
 * @version 2.2 Added running tab of reconciled charges to page
 * @version 2.3 Retain state of checked rows when table sort is applied or new expense added
 */
// GLOBALS
var $chkbox = $('input:checkbox[id^=chg]');         // all checkboxes on page
var $rows = $('table.sortable tbody').find('tr');   // all rows in table 
var curr_rec;        // current sum of reconciled items (those checked)
var curr_not;        // current sum of non-reconciled items (those not checked)
// array contents are retained for table sort and new expense items
if (window.localStorage.getItem("xids") === null) {
    var checkedIds = []; // array of expense Id's for all checked items 
    window.localStorage.setItem('xids', JSON.stringify(checkedIds));
} else {
    var checkedIds = JSON.parse(window.localStorage.getItem('xids'));
    tableInit();
}
/**
 * Whenever a new expense is added to the list, the page is reloaded with unchecked
 * checkboxes. It must be re-initialized to the state indicated by checkedIds
 */
function tableInit() {
    if (checkedIds.length > 0) {
        $rows.each(function() {
            var $tds = $(this).children();
            var $cbx = $($tds[4]).children().eq(0); // the checkbox
            var cbid = $cbx.val(); // checkbox id
            if (checkedIds.indexOf(cbid) !== -1) {
                $tds.each(function() {
                    $(this).css('background-color', 'blanchedalmond');
                });
                $cbx.prop('checked', true);
            }
        });
    }
}



$(function() {

colorizeAndSum();
/**
 * Get the current sum of unchecked boxes in the reconcile table
 * 
 * @return {null}
 */
function summer() {
    curr_rec = 0;
    curr_not = 0;
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
 * Apply coloring to checked item and sum the unchecked items;
 * Retain the state of checked items so that table sort doesn't lose it
 * 
 * @return {null}
 */
function colorizeAndSum() {
    $chkbox.each(function() {
        var cbox_item = $(this).val();  // the expense id of the item
        var indx = checkedIds.indexOf(cbox_item); // location of item in checkedIds
        if ($(this).is(':checked')) { 
            if (indx === -1) {
                // a new cbox is being checked
                var $csibs = $(this).parent().siblings();
                $csibs.each(function() {
                    $(this).css('background-color', 'blanchedalmond');
                });
                checkedIds.push(cbox_item);
            }
        } else {
            if (indx !== -1) {
                // item is already listed
                var $usibs = $(this).parent().siblings();
                $usibs.each(function() {
                    $(this).css('background-color', 'white');
                });
                checkedIds.splice(indx, 1);
            }
        }
        summer();
        window.localStorage.setItem('xids', JSON.stringify(checkedIds))
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

$chkbox.on('click', function() {
    colorizeAndSum();
});

$('#reconcile_chgs').on('click', function() {
    window.localStorage.removeItem('xids');
    $('#form').trigger('submit');
});

});
