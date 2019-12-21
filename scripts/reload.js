$('#reload').on('click', function() {
    if (confirm("Do you really want to drop all tables and reload them?")) {
        if (hostIs !== 'localhost') {
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
        } else {
            window.open('drop_all_tables.php', "_blank");
        }
    }
});