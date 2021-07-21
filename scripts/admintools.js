/**
 * @fileoverview Implement admintool button functionality
 * @author Ken Cowles
 * @version 2.0 Added archiving
 */
$( function() {  // wait until document is loaded...

// Make select strings for loading archives
var yrstring = "Archive of ";
var selectyr = document.getElementById('ldyr')
for (let j=0; j<archives.length; j++) {
    let avail_yr = yrstring + archives[j];
    let opt = document.createElement('option');
    opt.value = archives[j];
    opt.innerHTML = avail_yr;
    selectyr.appendChild(opt);
}

// Export database
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
$('#show').on('click', function()  {
    window.open('show_tables.php', "_blank_");
});
var arch_rdy = true;
$('#arch').on('click', function() {
    if ($('#achoice').css('display') === 'none') {
        $('#achoice').show();
    } else {
        $('#achoice').hide();
    }
});
$('#ayr').on('change', function() {
    let yr = $(this).val();
    if (yr !== 'x') {
        if (archives.indexOf(yr) !== -1) {
            alert("This year already has been archived");
            arch_rdy = false;
        } else {
            arch_rdy = true;
        }
    }
});
$('#mkarch').on('click', function() {
    let archiveyr = $('#ayr').val();
    if (archiveyr === 'x') {
        alert("No year has been selected");
        return;
    } else {
        if (arch_rdy) {
            $proceed = confirm("NOTICE: The data in the 'Charges' table will\n no longer" +
                " contain data from " + archiveyr);
            if ($proceed) {
                $.ajax({
                    url: 'chargesArchive.php?yr=' + archiveyr,
                    method: 'get',
                    success: function() {
                        alert(archiveyr + " has been archived and is saved in\n" +
                            "the database directory as 'Year" + archiveyr + ".sql'");
                    },
                    error: function(jqXHR) {
                        alert("Failed to make archive");
                    }
                });
            } else {
                return;
            }
        } else {
            alert("This year already has been archived");
        }
    }
});
$('#ldarch').on('click', function() {
    if ($('#ldayr').css('display') === 'none') {
        $('#ldayr').show();
    } else {
        $('#ldayr').hide();
    }
});
$('#larch').on('click', function() {
    let loadyr = $('#ldyr').val();
    if (loadyr === 'x') {
        alert("No year has been selected");
        return;
    }
    let script = 'loadArchive.php?arch=' + loadyr;
    $.ajax({
        url: script,
        method: 'get',
        success: function(result) {
            if (result === 'Done') {
                let ans = confirm("Archive has been loaded and `Charges` table updated\n" +
                    "Do you wish to delete the " + loadyr +" database table?");
                if (ans) {
                    let tbl2drop = "dropArchive.php?drop=" + loadyr;
                    $.get(tbl2drop, function() {
                        alert(loadyr + " table dropped from database");
                    });
                }
            } else if (result === 'Previously loaded') {
                alert("This archive was already loaded");
            } else {
                alert("Problem: " + result);
            }
        },
        error: function() {
            alert("Ajax error");
        }
    });
});
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

});  // end of doc loaded
