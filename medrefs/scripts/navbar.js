/**
 * @fileoverview This script executes navigation choices as well as page
 * displays. There are currently only two types of tables that will be 
 * displayed on main.php: 1) List Page; and 2) Edit List Page, hence
 * the navigation code handles both.
 * 
 * @author Ken Cowles
 * @version 1.0 First release
 */
 $(function () {

// bootstrap modals tied to menu action items
var menu_add = new bootstrap.Modal(document.getElementById('useradd'), {
    keyboard: false
});
var menu_ren = new bootstrap.Modal(document.getElementById('userren'), {
    keyboard: false
});
var menu_del = new bootstrap.Modal(document.getElementById('userdel'), {
    keyboard: false
});
var menu_def = new bootstrap.Modal(document.getElementById('userdef'), {
    keyboard: false
});

var pg_type = $('#page_type').text();
if (pg_type === 'main') {
    var tbl_width = 2 * parseInt($('#tbl_width').text());
    $('#active_display').width(tbl_width);
}
/**
 * Various HTML entities for customizing page modes
 */
var edit_btn = '<button id="edit" type="button" class="btn btn-secondary">' +
    '<span id="multibtn">Edit Page</span></button>';
var submit_btn = '<button type="submit" class="btn btn-secondary">Save Edits</button>';
var add_row = '<button id="addem" type="button" class="btn btn-secondary">' +
    'Add Row</button>';
var new_row = '<td><textarea class="tas" name="box';
var row_end = '"></textarea></td>';
var ids = 'rdqe';

var active = parseInt($('#item_no').text()); // not 0-based
var menu_list = $('#menu_list').text();
var menuArr = menu_list.split("|");
var selected = menuArr[active-1];
var pg_cnt = parseInt($('#pg_cnt').text());
var upages = pg_cnt ? $('#upages').text() : '';
var pg_lst = pg_cnt ? upages.split('|') : [];
window.setOfPages = []; // need global for jQuery .each loop (scope issue)
// create as array of integers:
pg_lst.forEach(function(page) {
    window.setOfPages.push(parseInt(page));
});
var url = 'page_content.php';
// functions...
const displayPage = (ajaxdata) => {
    $.post(url, ajaxdata, function(display) {
        if (ajaxdata.mode === 'edit') {
            $('#editor').append(form_el);    // add detached form element to 'editor' div
            $('#ino').val(active);
            $('#nme').val(selected);
            $('#active_display').empty();    // empty the current table
            var contents = $('#content').detach();  // detach the 'content' div
            $('#save_edits').append(contents);      // re-attach the content div to the form el
            $('#multibtn').text('Save Edits');  // change 'Edit Page' to 'Save Edits'
            $('#edit').replaceWith(submit_btn); // use form submit button
            $('button[type=submit]').after(add_row);
        }
        $('#active_display').html(display);
        if (ajaxdata.mode === 'display') {
            if ($('#edit').length !== 0) {
                $('#edit').remove();
            }
            $('#active_display').after(edit_btn);
            $('#center').text(menuArr[active-1]);
        } else {
            $('input[id^=rcb').after("<span class='adj'>DEL<span>");
        }
    }, 'html');
    return;
};
/**
 * This function will only be invoked if pg_type = 'main'
 * 
 * @param {integer} active The current menu item selected (1-based)
 *  
 * @returns {null} 
 */
const setContent = (active) => {
    let lgth = window.setOfPages.length;
    let fred = window.setOfPages.indexOf(active);
    alert("Qty: " + lgth + "; Result: " + fred +
        "; [0]: " + window.setOfPages[0] + "; [1]" + window.setOfPages[1]);
    if (window.setOfPages.indexOf(active) !== -1) {
        // get the active page for display
        var ajaxdata = {mode: 'display', menu: active};
        displayPage(ajaxdata);
    } else {
        alert("Ya boss");
        // go directly to the user_form to create the active page's list
        let selected_item = menuArr[active-1];
        let new_list = "user_form.php?menu=" + active + "&item=" +
            selected_item + "&new=no";
        window.open(new_list, "_self");
    }
    return;
};
const saveActive = (pageno) => {
    let url = "saveActive.php?no=" + pageno;
    $.ajax({
        url: url, 
        method: "get",
        dataType: "text",
        success: function(results) {
            if (results !== 'ok') {
                alert("Invalid menu item number")
            } else {
                if (pg_type === 'main') {
                    setContent(pageno);
                } else {
                    window.open("main.php", "_self");
                }
            }
        },
        error: function(_jqXHR, _error, _text) {
            alert("Halt");
        }
    });
}

/**
 * The form will only be used to save the page when in edit mode
 * It is detached from the page and utilized as needed
 */
var form_el = $('#save_edits').detach();

// only set content for display or edit, not for user_form.php
if (pg_type === 'main') {
    setContent(active);  // on page load of main.php
} 

$('body').on('click', '#edit', function() {
    if ($('#multibtn').text() === 'Edit Page') {
        var ajaxdata = {mode: 'edit', menu: active};
        displayPage(ajaxdata);
    } 
});
$('body').on('click', '#addem', function() { // add a row in edit mode
    // get last <td> id no
    let $rows = $('#active_display tbody').find('tr');
    let last  = $rows.length;
    let adder = '<tr>';
    for (let n=0; n<4; n++) {
        if (n !== 3) {
            adder += new_row + (n+1) + '[]" id="' + ids.charAt(n) + last + row_end;
        } else {
            adder += new_row + (n+1) + '[]" id="ed' + last +
            '"></textarea><input type="checkbox" class="rcbs" id="rcb' + last + '"></td>>'
        }
    }
    adder += '</tr>';
    var rcbid = 'input[id=rcb' + last + ']';
    $('tbody').append(adder);
    $(rcbid).after("<span class='adj'>DEL<span>");
});

// highlighting menu choice as 'active' (bootstrpap class): default => none active
var $usr_items = $('.uitems[id^=u]'); // <a> elements of user's current menu items
$('.uitems').each(function(indx) {
    if (indx === (active-1)) { // 0-based array
        $(this).addClass('active');
    }
    $(this).on('click', function() {
        $usr_items.removeClass('active');
        $(this).addClass('active');
        let a_id = $(this).attr('id');
        // new 'active'
        active = parseInt(a_id.substring(1)) + 1;
        saveActive(active);
    });
    return;
});
// in edit mode, rows can be removed
$('body').on('click', '.rcbs', function() {
    $(this).parent().parent().remove();
});

// Menu Manager
var editScript = "../menu/editMenu.php";
$('#addmenu').on('click', function() {
    menu_add.show();
});
$('body').on('click', '#addit', function() {
    let new_name = $('#madd').val();
    if (new_name == '') {
        alert("You have not entered an item to add to the menu");
        return false;
    }
    let ajaxdata = {action: 'add', data: new_name};
    $.post(editScript, ajaxdata, function(result) {
        if (result !== 'ok') {
            alert(result + ": Something went wrong - try again");
            return false;
        } else {
            alert("Item added!");
            menu_add.hide();
            location.reload();
        }
        
    });
});
$('#renmenu').on('click', function() {
    menu_ren.show();
});
$('body').on('click', '#chgit', function() {
    var item = $('#rensel').val();
    var newname = $('#newname').val();
    if (newname == '') {
        alert("You have not entered a new name for the item");
        return false;
    }
    let ajaxdata = {action: 'rename', select: item, data: newname};
    $.post(editScript, ajaxdata, function(result) {
        if (result !== 'ok') {
            alert("Something went wrong - try again");
        } else {
            alert("Item renamed!");
            location.reload();
        }
        menu_add.hide();
    });
});
$('#delmenu').on('click', function() {
    menu_del.show();
});
$('body').on('click', '#delit', function() {
    let goner = $('#delsel').val();
    let ans = confirm("Are you sure you want to delete " + goner + "?");
    if (ans) {
        let ajaxdata = {action: 'delete', select: goner};
        $.post(editScript, ajaxdata, function(result) {
            if (result === 'ok') {
                alert("Item deleted");
                location.reload();
            } else {
                alert(result + ": Something went wrong - try again");
            }
        });
    } else {
        return false;
    }
});
$('#spechome').on('click', function() {
    menu_def.show();
});
$('body').on('click', '#defhome', function() {
    let newhome = $('#defsel').val();
    let ajaxdata = {action: 'home', select: newhome};
    $.post(editScript, ajaxdata, function(result) {
        if (result === 'ok') {
            alert("Action completed");
            location.reload();
        } else {
            alert(result + ": Something went wrong - try again");
        }
    });
});


});
