/**
 * @fileoverview Manage the functionality of the Non-monthly Accts page; every time
 *               a new addition is made, a new row appears
 * 
 * @author Ken Cowles
 * @version 1.0 Release
 */
// parent window
var parent = window.opener;
/**
 * Prior to submitting, two steps are taken:
 *  1. Certain selects must be rendered for posting (both existing and new)
 *  2. For new entries, rows with data must be completely specified
 */

// inputs to replace 'un-postable' selects:
var sayr_input = '<input type="hidden" name="oyrs[]" value="" />';
var apay_input = '<input type="hidden" name="oap[]" value="" />';
var nocc_input = '<input type="hidden" name="nfreq[]" value="" />';
var nmth_input = '<input type="hidden" name="nfirst[]" value="" />';
var nayr_input = '<input type="hidden" name="eyrs[]" value="" />';
var npay_input = '<input type="hidden" name="nap[]" value="" />';

// aptype selects will have option1 disabled [Check/Draft]
$('.aptype').each(function(indx) {
    let $card = $(this).children();
    if ($card.get(0).tagName === 'SELECT') {
        $card[0].options[1].disabled = true;
    }
});
// If window is too narrow, table needs to expand for <select>s
adjustWidth = () => {
    if (window.innerWidth < 1000) {
        $('table').width("98%");
    } else if (window.innerWidth < 1200) {
        $('table').width("94%");
    } else if (window.innerWidth < 1400) {
        $('table').width("90%");
    }
}
adjustWidth(); // on page load
$(window).on('resize', function() {
    adjustWidth();
});
/**
 * 1. Replace un-selected <selects> with hidden inputs, value ''
 * 
 * @returns {null}
 */
function renderSelects() {
    if ($('#old_entries').length > 0) {
        $old_rows = $('table#old_entries tbody tr');
        $old_rows.each(function(indx, row) {
            // NOTE: there is a hidden child at td0
            let $sayr_td = $(row).children().eq(5);
            let $apay_td = $(row).children().eq(6);
            let $sayr_select = $sayr_td.children().eq(0);
            if ($sayr_select.css('display') === 'none') {
                $sayr_select[0].remove();
                $sayr_td.prepend(sayr_input);
            }
            let $apay_select = $apay_td.children().eq(0);
            if ($apay_select.val() === 'Select') {
                $apay_select[0].remove();
                $apay_td.prepend(apay_input);
            }
        });
    }
    $new_rows = $('table#new_entries tbody tr');
    $new_rows.each(function(indx, newrow) {
        // NOTE: there is no hidden child at td0, but additional <select> checks
        let $ntds = $(newrow).children();
        // <td>s with selects:
        let $nocc_td = $ntds.eq(1);
        let $nmth_td = $ntds.eq(3);
        let $nayr_td = $ntds.eq(4);
        let $npay_td = $ntds.eq(5);
        let $nocc_select = $nocc_td.children().eq(0);
        if ($nocc_select.val() === 'Select Frequency') {
            $nocc_select[0].remove();
            $nocc_td.prepend(nocc_input);
        }
        let $nmth_select = $nmth_td.children().eq(0);
        if ($nmth_select.val() === '99') {
            $nmth_select[0].remove();
            $nmth_td.prepend(nmth_input);
        }
        let $nayr_select = $nayr_td.children().eq(0);
        if ($nayr_select.css('display') === 'none') {
            $nayr_select[0].remove();
            $nayr_td.prepend(nayr_input);
        }
        let $npay_select = $npay_td.children().eq(0);
        if ($npay_select.val() === 'Select') {
            $npay_select[0].remove();
            $npay_td.prepend(npay_input);
        }
    });
}

/**
 * 2. For any new entries, ensure that the row has been completely filled
 * 
 * @returns {boolean}
 */
function validateNewData() {
    var $row_cells = $('table#new_entries tbody').find('tr');
    var partial = false;
    $row_cells.each(function(index) {
        var $td_cells = $(this).children();
        var $tds2check = $td_cells.slice(0, 5);
        var altyrs;
        $tds2check.each(function(indx) {
            console.log("Item " + indx);
            var $contents = $(this).children().eq(0);
            switch (indx) {
                case 0:
                    cells[0] = $contents.val() == '' ? false : true;
                    break;
                case 1:
                    cells[1] = $contents.val() == 'Select Frequency' ? false : true;
                    altyrs   = $contents.val() === 'Bi-Annually' ? true : false;
                    break;
                case 2:
                    cells[2] = $contents.val() == '' ? false : true;
                    break;
                case 3:
                    cells[3] = $contents.val() == '99' ? false : true;
                    break;
                case 4:
                    // the sense of cells[4] is inverted to simplify logic
                    if (altyrs) {
                        cells[4] = $contents.val() == 'Odd/Even?' ? false : true;
                    } else {
                        cells[4] = true;
                    }
            }
        });
        if (rowFilled(altyrs)) {
            alert("Row is good");
        } else if (!rowEmpty(altyrs)) {
            partial = true;
            alert("Row " + (index + 1) + " is not completed");
        }
        clearCells();
    });
    if (partial) {
        return false;
    } else {
        return true;
    }
}
var cells = [];
function clearCells() {
    cells[0] = cells[1] = cells[2] = cells[3] = cells[4] = false;
}
clearCells();

/**
 * For new entries, checking row states depends on whether or not a
 * payment frequency option has been set to 'Bi-Annually' [Every Other Year]
 * 
 * @param {boolean} alts 
 * @returns  {boolean}
 */
function rowFilled(alts) {
    if (alts) {
        return cells.every(element => element === true);
    } else {
        var noalts = cells.slice(0,4);
        return noalts.every(element => element === true);
    }
}
function rowEmpty(alts) {
    if (alts) {
        return cells.every(element => element === false);
    } else {
        var noalts = cells.slice(0,4);
        return noalts.every(element => element === false);
    }
}


/**
 * The following code handles the three buttons at the top of the page;
 * the 'return_type' hidden input informs 'saveCombo.php' which button
 * was clicked so that it can redirect appropriately.
 */
function formChecks(type) {
    $('#return_type').val(type);
    if (validateNewData()) {
        renderSelects();
        return true;
    } else {
        return false;
    }
}
$('#review').on('click', function(ev) {
    formChecks('self');
});
$('#savit').on('click', function(ev) {
    ev.preventDefault();
    if (formChecks('budget')) {
        $('form').trigger("submit");
    }
});
$('#nosave').on('click', function(ev) {
    ev.preventDefault();
    if (parent.closed) {
        window.open('../main/displayBudget.php', "_self")
    } else {
        window.opener.location.reload();    
        window.close();
    }
});
// Number of 'already-existing' entries in the database
var old_entries = parseInt($('#oldcnt').text());
if (old_entries > 0) { //initialize select boxes
    $('select[name^=ofreq]').each(function() {
        var ofval = $(this).next().val();
        $(this).val(ofval);
    });
    $('select[name^=ofirst]').each(function() {
        var fval = $(this).next().val();
        $(this).val(fval);
    });
}
// Check the new entries row for empty cells [only 0-4]

/**
 * Initialization of the editable table <select>s
 */
// payment frequency <select>
var $pay_opts = $('.opayfreq');
orgfreq.forEach(function(payfreq, indx) {
    $pay_opts[indx].value = payfreq;
});
$pay_opts.on('change', function() {
    var yr_cell = $(this).parent().next().next().next().children().eq(0);
    // when 'Every Other Year' is selected, show the Odd/Even select box
    if ($(this).val() === 'Bi-Annually') {
        yr_cell[0].value = "Odd/Even?";
        yr_cell.show();
    } else {
        yr_cell.hide();
    }
});
// Payment due month <select>
var $first_mo = $('.omonth');
orgmonth.forEach(function(due_mo, indx) {
    $first_mo[indx].value = due_mo;
});
// Alternate year choice <select>
var $yr_choice = $('.oyears');
orgsa.forEach(function(choice, indx) {
    if (choice == '') {
        $yr_choice[indx].value = "Odd/Even";
        $yr_choice[indx].style.display = "none";
    } else {
        $yr_choice[indx].value = choice;
    }
});
// Autopay type <select>
var $autopay = $('.old_ap');
orgtypes.forEach(function(type, indx) {
    if (type == '') {
        $autopay[indx].value = "Select";
    } else {
        $autopay[indx].value = type;
    }
});
// Rows to be deleted will be highlighted in grey
$('.dels').each(function() {  // .dels belongs to the checkbox <td>
    var $td = $(this);
    var $chkbox  = $td.children().eq(0);
    var $allTds  = $td.parent().children();
    $chkbox.on('click', function() {
        if ($(this).is(':checked')) {
            $allTds.each(function() {
                $(this).css('background-color', 'lightgrey');
            });
        } else {
            $allTds.each(function() {
                $(this).css('background-color', 'white');
            });
        }
    });
});

/**
 * This section pertains only to the 'New rows' table
 */
var newfreqs;
function specYrs() {
    newfreqs = null;
    newfreqs = $('.npayfreq');
    newfreqs.on('change', function() {
        var yr_cell = $(this).parent().next().next().next().children().eq(0);
        // when 'Every Other Year' is selected, show the Odd/Even select box
        if ($(this).val() === 'Bi-Annually') {
            yr_cell.show();
        } else {
            yr_cell.hide();
        }
    });
}
specYrs();


/**
 * HTML elements needed when adding rows
 */
var nrows = 1; // next row id
var $tbl  = $('table#new_entries tbody');
var $row  = $tbl.find('tr').eq(0);
var $tds  = $row.children();
var $sel1 = $tds.eq(1).children().eq(0).clone();
var $sel2 = $tds.eq(3).children().eq(0).clone();
var $sel3 = $tds.eq(4).children().eq(0).clone();
var $sel4 = $tds.eq(5).children().eq(0).clone();
var $td1  = $('<td class="add1"><input type="text" name="nitem[]" placeholder="Expense Item" /></td>');
var $td2  = $('<td class="add2"></td>');
$td2.append($sel1);
var $td3  = $('<td class="add3"><input type="text" name="namt[]" placeholder="Amount" /></td>');
var $td4  = $('<td class="add4 rms"></td>');
$td4.append($sel2);
var $td5  = $('<td class="sayr rms"></td>');
$td5.append($sel3);
var $td6  = $('<td class="aptype rms"></td>');
$td6.append($sel4);
var $td7  = $('<td class="apday"><input type="text" name="napday[]" value="" /></td>');
var $td8  = $('<td style="display:none;">Not Filled</td>');


$('#newrow').on('click', addRow);
/**
 * This row will appear below the last row in the new entries section
 * 
 * @return {null}
 */
function addRow() {
    var $nextrow = $('<tr />');
    var statid = "f" + nrows;  // status = Not Filled or Filled
    $td8.attr('id', statid);
    var rowid = "new" + nrows++;
    $nextrow.attr('id', rowid);
    $nextrow.addClass('itemrow');
    $nextrow.append($td1.clone());
    $nextrow.append($td2.clone());
    $nextrow.append($td3.clone());
    $nextrow.append($td4.clone());
    $nextrow.append($td5.clone());
    $nextrow.append($td6.clone());
    $nextrow.append($td7.clone());
    $nextrow.append($td8.clone());
    $tbl.append($nextrow);
    specYrs();
    return;
}
