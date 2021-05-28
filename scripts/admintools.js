$( function() {  // wait until document is loaded...

$('#exall').on('click', function() {
    window.open('export_all_tables.php?dwnld=N', "_blank");
});

function retrieveDwnldCookie(dcname) {
    var parts = document.cookie.split(dcname + "=");
    if (parts.length == 2) {
        return parts.pop().split(";").shift();
    }
}
$('#reload').on('click', function() {
    if (confirm("Do you really want to drop all tables and reload them?")) {
        // backup, just in case...
        /*
        window.open('export_all_tables.php?dwnld=N', "_blank");
        var dwnldResult;
        var downloadTimer = setInterval(function() {
            dwnldResult = retrieveDwnldCookie('DownloadDisplayed');
            if (dwnldResult === '1234') {
                clearInterval(downloadTimer);
                if (confirm("Proceed with reload?")) {
                    window.open('drop_all_tables.php', "_blank");
                }
            }
        }, 1000)
        */
       window.open('drop_all_tables.php', "_blank");
    }
});

$('#drall').on('click', function() {
    if (confirm("Do you really want to drop all tables?")) {
        window.open('drop_all_tables.php?no=all', "_blank");
    }
});
$('#ldall').on('click', function() {
    window.open('load_all_tables.php', "_blank");
});
/*
$('#swdb').on('click', function() {
    window.open('switchDb.php');
    window.close();
});
$('#emode').on('click', function() {
    var butnTxt = $('#emode').text();
    $.ajax({
        url: 'siteEdit.php',
        data: {button: butnTxt},
        dataType: "text",
        success: function(resp) {
            $('#emode').text(resp);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("Edit mode change script failed: " +
                textStatus + ": " + errorThrown);
        }
    });
});
$('#commit').on('click', function() {
    $.ajax({
        url: 'commit_number.txt',
        dataType: 'text',
        success: function(resp) {
            alert("The current commit number\nassociated" +
                " with this site is:\n\n\t" + resp);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("The following error resulted in admintools.js:\n" 
                + "Error: " + textStatus + ": " + errorThrown);
        }
    });
});
$('#cleanPix').on('click', function() {
    window.open('cleanPix.php', "_blank");
});
*/
$('#version').on('click', function() {
    $.ajax({
        url: 'version.php',
        method: 'get',
        success: function(result) {
            alert(result);
        },
        error: function() {
            alert("Could not retrieve PHP version info");
        }
    });
});
$('#phpinfo').on('click', function() {
    window.open('phpInfo.php', "_blank");
});
$('#lo').on('click', function() {
    $.ajax({
        url: 'logout.php',
        method: 'get',
        success: function() {
            window.open('../index.php', "_self");
        }
    });
});
/*
$('#pub').on('click', function() {
    window.open("reldel.php?act=rel", "_blank");
});
$('#lst').on('click', function() {
    window.open("list_new_files.php?request=files", "_blank")
});
$('#ehdel').on('click', function() {
    window.open("reldel.php?act=del","_blank");
});
$('#show').on('click', function()  {
    window.open('show_tables.php', "_blank_");
});
$('#drop').on('click', function() {
    var dtarg = 'drop_table.php?tbl=' + $('#dtbl').val();
    window.open(dtarg, "_blank");
});
$('#create').on('click', function() {
    var ctype = $('#ctbl').val(); // the table name to create
    ctarg = "create_table.php?tbl=" + ctype;
    window.open(ctarg, "_blank");
});
$('#sgls').on('click', function() {
    // not yet implemented
});
var disp = $('#dstat').text();
if (disp === 'Open') {
    $('#modeopt').css('display','block');
} else {
    $('#modeopt').css('display','none');
}
var j = 0;
$('input[type=checkbox]').each( function() {
    if (cbs[j] === 'Y') {
        $(this).attr('checked','checked');
    }
    j++;
});
$('#mode').on('click', function() {
    $('#modeopt').slideToggle();
});
if (typeof(nopix) !== 'undefined') {
    alert(nopix);
}
$('#ldet').on('click', function() {
    alert("Not yet implemented");
});
$('#addbk').on('click', function() {
    window.open("addBook.php", "_blank");
})
*/

});  // end of doc loaded
