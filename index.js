/**
 * @fileoverview To overcome some CSS limitations, properties are set within
 * 
 * @author Ken Cowles
 * @version 1.0 First release of new intro page
 */
$(function() {

const styleSetup = () => {
    var bodymargin = $('body').css('margin-top');
    var topmargin = parseInt(bodymargin);
    var containerht = $('.hcontainer').height();
    var divmargin = 4;
    var borderwidth = $('#logger').css('border-top-width');
    var borders = 2 * parseInt(borderwidth);
    var winheight = window.innerHeight;
    var logheight = winheight - 2 * topmargin - containerht - divmargin - borders
    $('#logger').height(logheight);
}
styleSetup();
window.onresize = function() {
    styleSetup();
}

// forgot password modal
var usrmail = $('#usr_modal').detach();
$('#resetpass').on('click', function(ev) {
    ev.preventDefault();
    var def = $.Deferred();
    modal.open({id: 'usrmail', height: '120px', width: '240px',
        content: usrmail, deferred: def});
});

$('form').submit(function(ev) {
    ev.preventDefault();
    let usr = $('input[name=username]').val();
    let pass = $('input[name=password]').val();
    if (usr == '') {
        alert("Please supply a valid username");
        return false;
    }
    if (pass == '') {
        alert("Please supply a valid passworkd");
        return false;
    }
    validateUser(usr, pass);
});

});