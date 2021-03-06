/**
 * @fileoverview The login state is determined via getLogin.php; that script
 * stores '#cookiestatus' and '#startpg' on index.php
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
$('input[name=username]').val(''); // clear login for refresh/reload
var cookies = navigator.cookieEnabled ? true : false;
var user_cookie_state = $('#cookiestatus').text();
var startpg = $('#startpg').text();
var completed1 = startpg.charAt(0) === '1' ? true : false;
var completed2 = startpg.charAt(1) === '1' ? true : false;
var completed3 = startpg.charAt(2) === '1' ? true : false;

if (cookies) {
    if (user_cookie_state === 'NONE') {
        alert("Valid user registration cannot be located");
    } else if (user_cookie_state === 'EXPIRED') {
        var ans = confirm("Your password has expired\n" + 
            "Would you like to renew?");
        if (ans) {
            renewPassword('renew', 'expired');
        } else {
            renewPassword('norenew', 'expired');
        }
    } else if (user_cookie_state === 'RENEW') {
        var ans = confirm("Your password is about to expire\n" + 
            "Would you like to renew?");
        if (ans) {
            renewPassword('renew', 'valid');
        } else {
            renewPassword('norenew', 'valid');
        }
    } else if (user_cookie_state === 'MULTIPLE') {
        alert("There are multiple accounts registered for this login:\n"
            + "Please contact the site master");
    } else if (user_cookie_state === 'OK') {
            if (completed1 && completed2 && completed3) {
                var homepg = "main/displayBudget.php";
                window.open(homepg, "_self");
            } else {
                var startpoint = 'edit/newBudgetPanels.php?pnl=' + startpg;
                window.open(startpoint, "_self");
            }
    } 
} else {  // cookies disabled
    alert("Cookies are disabled on this browser:\n" +
        "You will need to login with each visit");
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
            if (status.indexOf('LOCATED') !== -1) {
                alert("You are logged in");
                var pos = status.indexOf('&') + 1;
                var startpg = status.substr(pos);
                var cookie_choice = status.substr(pos+4);
                var proceed = $.Deferred();
                var ajaxdata = {username: usr_name};
                if (cookie_choice === 'accept') {
                    $.ajax({
                        url: "../admin/sendcookie.php",
                        data: ajaxdata,
                        method: "post",
                        success: function() {
                            proceed.resolve();
                        },
                        error: function() {
                            alert("cookie script failed in login");
                            proceed.reject();
                        }
                    });
                } else {
                    proceed.resolve();
                }
                $.when(proceed).then(function() {
                    if(startpg.charAt(0) === '1' && startpg.charAt(1) === '1'
                        && startpg.charAt(2) === '1'
                    ) {
                        var homepg = "main/displayBudget.php"
                        window.open(homepg, "_self");
                    } else {
                        var startpoint = 'edit/newBudgetPanels.php' +
                            '?pnl=' + startpg;
                        window.open(startpoint, "_self");
                    }
                });
            } else if (status.indexOf('RENEW') !== -1) {
                // in this case, the old cookie has been set pending renewal
                var renew = confirm("Your password is about to expire\n" + 
                    "Would you like to renew?");
                if (renew) {
                    renewPassword('renew', 'valid');
                } else {
                    renewPassword('norenew', 'valid');
                }
            } else if (status.indexOf('EXPIRED') !== -1) {
                var renew = confirm("Your password has expired\n" +
                    "Would you like to renew?");
                if (renew) {
                    renewPassword('renew', 'expired');
                } else {
                    renewPassword('norenew', 'expired');
                }
            } else if (status.indexOf('BADPASSWD') !== -1) {
                var msg = "The password you entered does not match " +
                    "your registered password;\nPlease try again";
                alert(msg);
                $('#passin').val('');
            } else { // no such user in USERS table
                var msg = "Your registration info cannot be uniquely located:\n" +
                    "Please click on the 'Sign me up!' link to register";
                alert(msg);
                $('#user').val('');
                $('#passin').val('');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("Error encountered in validation: " +
                textStatus + "; Error: " + errorThrown);
        }
    });
}
// for renewing password/cookie
function renewPassword(update, status) {
    if (update === 'renew') {
        var renewpg = "admin/renew.php";
        window.open(renewpg, "_self");
    } else {
        // if still valid, refresh will display login, otherwise do nothing
        if (status === 'valid') {
            var homepg = "main/displayBudget.php";
            window.open(homepg, '_self');
        } else {
            $.get("../admin/logout.php");
            alert("You have been logged out and must now re-register");
        }
    }
}
