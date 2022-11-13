/**
 * @fileoverview Implement admintool button functionality
 * 
 * @author Ken Cowles
 * @version 1.0 First release
 */
$( function() {  // wait until document is loaded...

// Export database
$('#exall').on('click', function() {
    window.open('export_all_tables.php?dwnld=N', "_blank");
    $(this).blur();
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
       $(this).blur();
    }
});

$('#drall').on('click', function() {
    if (confirm("Do you really want to drop all tables?")) {
        window.open('drop_all_tables.php?no=all', "_blank");
        $(this).blur();
    }
});
$('#show').on('click', function()  {
    window.open('show_tables.php', "_blank_");
    $(this).blur();
});
$('#version').on('click', function() {
    var $btn = $(this);
    $.ajax({
        url: 'version.php',
        method: 'get',
        success: function(result) {
            alert(result);
            $btn.blur();
        },
        error: function() {
            alert("Could not retrieve PHP version info");
        }
    });
});
$('#phpinfo').on('click', function() {
    window.open('phpInfo.php', "_blank");
    $(this).blur();
});

});  // end of doc loaded
