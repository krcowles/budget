/**
 * @fileoverview Modify the page by adding rows or extra boxes
 * 
 * @author Ken Cowles
 * @version 1.0 First release
 */
$(function () {

// default number of rows to add for each click of the button "Add rows"
const default_count = 4;
// have any saves been made?
var wasSaved = $('#saved').text() === 'y' ? true : false;
var unsavedChanges = false;
// row template clone:
var row_clone = $('#rowadder').html();

// Return to main page button
$('#return').on('click', function() {
    if (!wasSaved) {
        alert("Nothing has been saved; To return without saving\n" +
            "Select a different menu item");
            return false;
    } else if (wasSaved && unsavedChanges) {
        let ans = confirm("You have unsaved changes. Do you wish to exit without saving?");
        if (ans) {
            window.open("main.php", "_self");
        } else {
            return false;
        }
    } else {
        window.open("main.php", "_self");
    }
});

// checkbox for adding column of boxes on right side
$('#add_col').on('click', function() {
    if ($(this).is(':checked')) {
        $('td.box4').css('display', 'inline-block');
        $('label').text("Remove Boxes");
        $('#toggled').css('visibility', 'visible');
        $('#col4').css('visibility', 'visible');

    } else {
        $('td.box4').css('display', 'none');
        $('label').text("Add Boxes");
        $('#toggled').css('visibility', 'hidden');
        $('#col4').css('visibility', 'hidden');
    }
});
// check or uncheck the 'Add Rows' checkbox on page load?
if ($('#chk_adder').text() === 'check') {
    $('#add_col').trigger('click');
}
// Position checkbox at end of col3 (col4 may be 'not visible'
//  and therefore inaccessible from javascript
var $th_els   = $('th');
var third_el  = $($th_els[2]).offset();
var third_pos = parseInt($($th_els[2]).width());
var chkbox_pos = third_pos + third_el.left;
$('#chkbox').offset({left: chkbox_pos});

// adding rows to the table...
$('#more_rows').on('click', function(ev) {
    ev.preventDefault();
    let row_cnt = parseInt($('#max_row').text());
    let new_cnt = row_cnt + default_count;
    $('#max_row').text(new_cnt);
    var $box4stat = $('.box4');
    var col4shown = $($box4stat[0]).css('display') === 'none' ? false : true;
    for (let i=row_cnt; i<new_cnt; i++) {
        $row = $(row_clone);
        $tds = $row.children();
        b4_id = "unchanged";
        for (let j=0;j<default_count;j++) {
            var $input_item = $($tds[j]).children().eq(0);
            var inp_id;
            switch (j) {
                case 0:
                    inp_id = 'r' + i;
                    break;
                case 1:
                    inp_id = 'd' + i;
                    break;
                case 2:
                    inp_id = 'q' + i;
                    break;
                case 3:
                    inp_id = 'a'  + i;
            }
            $input_item.attr('id', inp_id);
        }
        $('#refs tbody').append($row);
        if (col4shown) {
            $($tds[3]).css({
                visibility: 'visible',
                display: 'inline-block'
            });
        }
    }
});


});