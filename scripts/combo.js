/**
 * @fileoverview Manage the functionality of the Non-monthly Accts page; every time
 *               a new addition is made, a new row appears
 * 
 * @author Ken Cowles
 * @version 1.0 Release
 */
// parent window
var parent = window.opener;

// Form submission check:
$('#review').on('click', function() {
    $('#return_type').val("self");
});
$('#savit').on('click', function() {
    $('#return_type').val("budget");
    // check for unfilled cells
    if (cell1 || cell2 || cell3 || cell4) {
        var msg = "You have a partially filled last row; Click 'OK' to clear it." +
            " If you select 'Cancel' you must complete the row in order to Save";
        var clearit = confirm(msg);
        if (clearit) {
            var $clear_tds = $('tbody tr[id=new' + newbies + ']').children();
            $($clear_tds[0]).children().eq(0).val('');
            $($clear_tds[1]).children().eq(0).val('0');
            $($clear_tds[2]).children().eq(0).val('');
            $($clear_tds[3]).children().eq(0).val('0');
            var save = confirm("Do you now wish to save the data as is?");
            if (!save) {
                return false;
            }
        } else {
            return false;
        }
    }
});
// Return to Budget page if still extant
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
/**
 * HTML elements needed when adding rows
 */
var tbl    = $('table#new_entries tbody');
var $sel1  = $('select[name^=freq]').clone();
var $sel2  = $('select[name^=first]').clone();
var $alts  = $('input[name^=alts]').clone();
var $td1   = $('<td class="add1"><input type="text" name="item[]" placeholder="Expense Item" /></td>');
var $td2   = $('<td class="add2"></td>');
$td2.append($sel1);
$td2.append($alts);
var $td3   = $('<td class="add3"><input type="text" name="amt[]" placeholder="Amount" /></td>');
var $td4   = $('<td class="add4"></td>');
$td4.append($sel2);

// For purpose of detecting if all cells have been specified before adding a row
var cell1 = false;
var cell2 = false;
var cell3 = false;
var cell4 = false;
var newbies;
var altyrs = 'Even';
lastRowSetup(); // initialize change detection for last row of newbies
/**
 * This function will enable change detection on the last (current) row of 
 * newbies.
 * 
 * @return {null}
 */
function lastRowSetup() {
    newbies = $('table#new_entries tbody tr[id^=new]').length;
    var lastid = newbies > 1 ? `tbody tr[id=new${newbies}]` : "tbody tr[id=new1]";
    var $lastcells = $(lastid).find('td');
    var $dat1 = $($lastcells[0]).children().eq(0); // input
    var $dat2 = $($lastcells[1]).children().eq(0); // select
    $dat2.attr('id', 'sel1');
    var $alt  = $dat2.next();
    $alt.attr('id', 'alt1');
    var $dat3 = $($lastcells[2]).children().eq(0); // input
    var $dat4 = $($lastcells[3]).children().eq(0); // select
    $dat4.attr('id', 'sel2');
    $dat1.on('change', function() {
        if (!cell1) {
            cell1 = true;
            if (cell2 && cell3 && cell4) {
                addRow();
            }
        }
    });
    $dat2.on('change', function() {
        var cursel = $('#sel1 option:selected').text();
        if ($('#sel1 option:selected').text() === 'Select Payment Frequency') {
            cell2 = false;
        } else {
            if (cursel === 'Every Other Year') {
            // Add place to indicate odd or even year payments{
                var choice = "Press 'OK' if Even year payments, 'Cancel' if Odd year payments";
                var sched = confirm(choice);
                altyrs = sched ? 'Even' : 'Odd';
                $('#alt1').val(altyrs);
            }
            cell2 = true;
            if (cell1 && cell3 && cell4) {
                addRow();
            }
        }
    });
    $dat3.on('change', function() {
        if (!cell3) {
            var no = parseFloat($(this).val());
            if (isNaN(no)) {
                alert("The value entered is not a number; Please enter a number (no commas)");
                $(this).val('');
                return;
            } else {
                if (no < 0) {
                    alert("No negative numbers are allowed");
                    $(this).val('');
                    return;
                }
                if (!Number.isInteger(no*100)) {
                    alert("Too many digits after the decimal point; Please correct");
                    return;
                }
            }
            cell3 = true;
            if (cell1 && cell2 && cell4) {
                addRow();
            }
        }
    });
    $dat4.on('change', function() {
        if ($('#sel2 option:selected').text() === 'Select Month') {
            cell4 = false;
        } else {
            cell4 = true;
            if (cell1 && cell2 && cell3) {
                addRow();
            }
        }
    });
    return;
}
/**
 * When it's time for the next newbie row to be added, remove the previous select box
 * id's, add the next row, and then redo the lastRowSetup()
 * 
 * @return {null}
 */
function addRow() {
    // remove id's from current selects
    $('#sel1').removeAttr('id');
    $('#sel2').removeAttr('id');
    $('#alt1').removeAttr('id');
    var $nextrow = $('<tr />');
    var rowid = "new" + ++newbies;
    $nextrow.attr('id', rowid);
    $nextrow.addClass('itemrow');
    /**
     * cloning is required!
     * append() will otherwise move the old element to the new location
     * this results in the old row having a height=0
     */ 
    $nextrow.append($td1.clone());
    $nextrow.append($td2.clone());
    $nextrow.append($td3.clone());
    $nextrow.append($td4.clone());
    tbl.append($nextrow);
    cell1 = cell2 = cell3 = cell4 = false;
    lastRowSetup();
    return;
}
$('.rms').each(function() {
    let $td = $(this);
    let $chkbox = $td.children().eq(0);
    $chkbox.on('click', function() {
        if ($(this).is(':checked')) {
            let $allTds = $td.parent().children();
            $allTds.each(function(i) {
                if (i > 0) {
                    let $contents = $(this).children().eq(0);
                    $contents.css({
                        color: 'grey',
                        backgroundColor: 'gainsboro'
                    });
                }
            });
            $td.parent().css('background-color', 'gainsboro');
        } else {
            let $allTds = $td.parent().children();
            $allTds.each(function(i) {
                if (i > 0) {
                    let $contents = $(this).children().eq(0);
                    $contents.css({
                        color: 'black',
                        backgroundColor: 'white'
                    });
                }
            });
            $td.parent().css('background-color', 'white');
        }
    });
});
