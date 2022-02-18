/**
 * @fileoverview To overcome some CSS limitations, properties are set within
 * 
 * @author Ken Cowles
 * @version 1.0 First release of new intro page
 * @version 2.0 Transfer to Mochahost
 * @version 3.0 Add login security/lockout mechanism
 */
$.get('accounts/lockStatus.php', function(status) {
    if (status !== "ok") {
        alert("Your 60 minute lockout period has not expired;\nTry again later");
        var $usrname = $('input[name=username]');
        $usrname.val('');
        $usrname.css('background-color', 'lightgray');
        $usrname.attr('disabled', 'disabled');
        var $passwd = $('input[name=password]');
        $passwd.val('');
        $passwd.css('background-color', 'lightgray');
        $passwd.attr('disabled', 'disabled');
        $('#submit').css('background-color', 'lightgray');
        $('#submit').attr('disabled', 'disabled');
    }
}, "text"); 

var chg_pass = new bootstrap.Modal(document.getElementById('resetemail'));
var sec_ques = new bootstrap.Modal(document.getElementById('twofa'));

$(function() {
/** 
 * This establishes space occupied by major elements on the page
 * 
 * @return {null}
 */
const divset = () => {
    let winht = window.innerHeight;
    let aht = .40 * winht;
    let bht = .60 * winht;
    $('#top-part').height(aht);
    $('#bottom-part').height(bht);
    let tooltxt = $('#learn').height();
    let lrnpad = 0.40 * tooltxt + 'px';
    let tlspad = 0.10 * tooltxt + 'px';
    $('#lrn').css('top', lrnpad);
    $('#tls').css('top', tlspad);
    return;
}
divset();
$(window).on('resize', function() {
    divset();
});

$('#resetpass').on('click', function() { // "Forgot User name and/or Password" link
    chg_pass.show();
});

$('#cpass').on('click', function() {
    let eaddr = $('#remail').val();
    let regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (!regex.test(eaddr)) {
        alert("You must enter a valid email address");
        return false;
    }
    eaddr.toLowerCase();
    let adata = {email: eaddr};
    $.ajax({
        url: '../accounts/sendmail.php',
        method: 'post',
        data: adata,
        dataType: 'text',
        success: function(result) {
            if (result === 'ok') {
                alert('An email has been sent');
            } else {
                alert('The email failed');
            } 
            chg_pass.hide();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            let msgtxt = "Error sending email:\n";
            let msg = msgtxt + textStatus + "; Error: " + errorThrown;
            alert(msg);
        }
    });
});


$('form').on('submit', function(ev) {
    ev.preventDefault();
    
    user = $('input[name=username]').val();
    let pass   = $('input[name=password]').val();
    if (user == '') {
        alert("Please supply a valid username");
        return false;
    }
    if (pass == '') {
        alert("Please supply a valid password");
        return false;
    }
    validateUser(user, pass);
});

});