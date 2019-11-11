$(function() {

$('#user').on('change', function() {
    var user = $(this).val();
    $(this).val("");
    var logdata = $('#log_modal').detach();
    modal.open({id: 'login', height: '62px', width: '280px', content: logdata,
        usr: user});
});

});