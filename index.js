/**
 * @fileoverview To overcome some CSS limitations, properties are set within
 * 
 * @author Ken Cowles
 * @version 1.0 First release of new intro page
 * @version 2.0 Transfer to Mochahost
 */
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

var chg_pass = new bootstrap.Modal(document.getElementById('resetemail'));
$('#resetpass').on('click', function() {
    chg_pass.show();
});

$('#cpass').on('click', function() {
    let eaddr = $('#remail').val();
    let adata = {email: eaddr};
    $.ajax({
        url: '../admin/sendmail.php',
        method: 'post',
        data: adata,
        dataType: 'text',
        success: function(result) {
            if (result === 'ok') {
                alert('An email has been sent');
            } else if (result === 'bad') {
                alert('The email address is not valid');
            } else if (result === 'nofind') {
                alert('Your email could not be located in our database');
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