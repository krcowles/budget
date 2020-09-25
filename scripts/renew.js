/**
 * @fileoverview This form will reset the user's password and update
 * the User table 'passwd_expire' field. If cookies are accepted, a
 * new cookie will be sent.
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
$(function() {
  
$('#form').validate({
    rules: {
        password: {
            minlength: 8,
        },
        confirm_password: {
            minlength: 8,
            equalTo: "#passwd"
        }
    },
    messages: {
        password: {
            minlength: "Passwords must be at least 8 characters"
        },
        confirm_password: {
            minlength: "Passwords must be at least 8 characters",
            equalTo: "Password does not match - please retry"
        }
    }
}); // end validate form

// prevent hitting 'Return' key submitting form - user must hit button
window.onkeydown = function(event) {
    if(event.keyCode == 13) {
        if(event.preventDefault) event.preventDefault();
        return false;
    }
}

// register user's choice for site cookies
var cookies = false;
$('#accept').on('click', function(ev) {
    ev.preventDefault();
    $('#choice').val('accept');
    cookies = true;
    $('#cookie_banner').slideToggle();
 });
 $('#reject').on('click', function(ev) {
    ev.preventDefault();
    $('#choice').val('reject');
    cookies = true;
    $('#cookie_banner').slideToggle();
 });

$('#form').on('submit', function(evt) {
    evt.preventDefault();
    if ($('#passwd').val() == '' || $('#confirm_password').val() == '') {
            alert("Password and Confirm Password must both be completed");
            return;
    }
    if ($('#passwd').val() !== $('#confirm_password').val()) {
        alert("The entries for 'Passwords' and 'Confirm' do not match");
        $('#confirm_password').val('');
        return;
    }
    if (!cookies) {
        alert("You must accept or reject the use of cookies for this site");
        return;
    }
    var ajaxData = new FormData();
    ajaxData.append('password', $('input[name=password]').val());
    ajaxData.append('cookies', $('#choice').val());
    ajaxData.append('submitter', 'renew');
    ajaxData.append('username', $('input[name=username]').val());
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'create_user.php');
    xhr.onload = function() {
        if (this.status !== 200) {
            if (this.response !== 'DONE') {
                alert("The password renewal/reset did not occur\n\n" +
                    "The following unexpected result occurred:\n" +
                    "Server returned status " + this.status);
            }
        } else {
            var success = "../index.php";
            window.open(success, '_self');
        }
    }
    xhr.onerror = function() {
        alert("The request failed: password renewal/reset did not occur\n" +
            "Contact the site master or try again.");
    }
    xhr.send(ajaxData);
});

});
