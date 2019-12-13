// timestamp: 7:00PM 11/17/2019
// are cookies enabled on this browser?
var cookies = navigator.cookieEnabled ? true : false;
// the next two variables are provided complements getLogin.php
var login_name = document.getElementById('usrcookie').textContent;
// if a login_name appears, cookies are already enabled:
var user_cookie_state = document.getElementById('cookiestatus').textContent;
// defaults to 'OK' unless a registered user is logged in with expiration issues
if (cookies) {
    if (user_cookie_state === 'NONE') {
        alert("No user registration has been located for " + login_name);
    } else if (user_cookie_state === 'EXPIRED') {
        var ans = confirm("Your password has expired\n" + 
            "Would you like to renew?");
        if (ans) {
            renewPassword(login_name, 'renew', 'expired');
        } else {
            renewPassword(login_name, 'norenew', 'expired');
        }
    } else if (user_cookie_state === 'RENEW') {
        var ans = confirm("Your password is about to expire\n" + 
            "Would you like to renew?");
        if (ans) {
            renewPassword(login_name, 'renew', 'valid');
        } else {
            renewPassword(login_name, 'norenew', 'valid');
        }
    } else if (user_cookie_state === 'MULTIPLE') {
        alert("There are multiple accounts associated with " + login_name +
            "\nPlease contact the site master");
    } else if (user_cookie_state === 'OK') {
        if (login_name !== 'none') {
            //alert("OK");
            var homepg = "main/displayBudget.php?user=" + login_name;
            window.open(homepg, "_self");
        } else { // login process
            $('#user').on('change', function() {
                var user = $(this).val();
                var logdata = $('#log_modal').detach();
                modal.open({id: 'login', height: '62px', width: '280px',
                    content: logdata, usr: user});
            });
        }
    } 
} else {  // cookies disabled
    alert("Cookies are disabled on this browser:\n" +
        "You will not be able to login or register for this site.\n" +
        "Please enable cookies to overcome this limitation");
}

// login authentication
function validateUser(usr_name, usr_pass) {
    $.ajax( {
        url: "admin/authenticate.php",
        method: "POST",
        data: {'usr_name': usr_name, 'usr_pass': usr_pass},
        dataType: "text",
        success: function(srchResults) {
            var status = srchResults;
            if (status.indexOf('LOCATED') >= 0) {
                alert("You are logged in");
                var homepg = "main/displayBudget.php?user=" + usr_name;
                window.open(homepg, "_self");
            } else if (status.indexOf('RENEW') >=0) {
                // in this case, the old cookie has been set pending renewal
                var renew = confirm("Your password is about to expire\n" + 
                    "Would you like to renew?");
                if (renew) {
                    renewPassword(usr_name, 'renew', 'valid');
                } else {
                    renewPassword(usr_name, 'norenew', 'valid');
                }
            } else if (status.indexOf('EXPIRED') >= 0) {
                var renew = confirm("Your password has expired\n" +
                    "Would you like to renew?");
                if (renew) {
                    renewPassword(usr_name, 'renew', 'expired');
                } else {
                    renewPassword(usr_name, 'norenew', 'expired');
                }
            } else if (status.indexOf('BADPASSWD') >= 0) {
                var msg = "The password you entered does not match " +
                    "your registered password;\nPlease try again";
                alert(msg);
                $('#passin').val('');
                textResult =  "bad_password";
            } else { // no such user in USERS table
                var msg = "Your registration info cannot be uniquely located:\n" +
                    "Please click on the 'Sign me up!' link to register";
                alert(msg);
                $('#user').val('');
                $('#passin').val('');
                textResult =  "no_user";
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("Error encountered in validation: " +
                textStatus + "; Error: " + errorThrown);
        }
    });
}
// for renewing password/cookie
function renewPassword(user, update, status) {
    var homepg = "main/displayBudget.php?user=" + user;
    if (update === 'renew') {
        var ajaxurl = 'admin/renew.php?user=' + user;
        $.ajax({
            url: ajaxurl,
            method: "POST",
            dataType: "text",
            success: function(results) {
                if (results == "OK") {
                    window.open(homepg, "_self");
                } else {
                    alert("Attempt to renew has failed");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                var msg = "An error occurred during renewal attempt:\n" +
                    "Error " + errorThrown + ": " + textStatus;
                alert(msg);
            }
        });
    } else {
        // if still valid, refresh will display login, otherwise do nothing
        if (status === 'valid') {
            window.open(homepg, '_self');
        }
    }
}
