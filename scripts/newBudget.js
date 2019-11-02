$(function() {

$('#save').on('click', function() {
    $('#form').submit();
});

$('#done').on('click', function(ev) {
    ev.preventDefault();
    window.open("../main/budget.php", "_self");
});
    
// check for pre-existing account data & form html for said data:
$.ajax({
    url: "getRawAccounts.php",
    method: "GET",
    dataType: "html",
    success: function(results) {
        if (results !== 'nofile') {
            $('#old').append(results);
            $('#old').css('display', 'block');
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        var msg = "In 'newBudget.js': Failed to get existing account data:\n" + 
            "Error " + errorThrown  + "/n" + textStatus;
        alert(msg);
    }
});

});