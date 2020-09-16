/**
 * @fileoverview The login state is determined via getLogin.php; that script
 * stores '#cookiestatus' and '#startpg' on index.php
 * 
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
$('#user').val(''); // clear login for refresh/reload
var cookies = navigator.cookieEnabled ? true : false;
var user_cookie_state = $('#cookiestatus').text();
var startpg = $('#startpg').text();
var completed1 = startpg.charAt(0) === '1' ? true : false;
var completed2 = startpg.charAt(1) === '1' ? true : false;
var completed3 = startpg.charAt(2) === '1' ? true : false;

// enable user logins
$('#user').on('change', function(ev) {
    // the following is required to correct a Safari bug:
    $(this).unbind('change');
    var user = $(this).val();
    var logdata = $('#log_modal').detach();
    modal.open({id: 'login', height: '72px', width: '260px',
        content: logdata, usr: user});
});
$('#forgot').on('click', function(ev) {
    ev.preventDefault();
    var def = new $.Deferred();
    $('#passtxt').css('display', 'none');
    var usrmail = $('#usr_modal').detach();
    modal.open({id: 'usrmail', height: '108px', width: '240px',
        content: usrmail, deferred: def});
    $.when(def).then(function() {
        $('#modal_wins').append(usrmail);
        $('#passtxt').css('display', 'inline');
    });
});

if (cookies) {
    if (user_cookie_state === 'NONE') {
        alert("Valid user registration cannot be located");
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
            } else if (status.indexOf('RENEW') !== -1) {
                // in this case, the old cookie has been set pending renewal
                var renew = confirm("Your password is about to expire\n" + 
                    "Would you like to renew?");
                if (renew) {
                    renewPassword(usr_name, 'renew', 'valid');
                } else {
                    renewPassword(usr_name, 'norenew', 'valid');
                }
            } else if (status.indexOf('EXPIRED') !== -1) {
                var renew = confirm("Your password has expired\n" +
                    "Would you like to renew?");
                if (renew) {
                    renewPassword(usr_name, 'renew', 'expired');
                } else {
                    renewPassword(usr_name, 'norenew', 'expired');
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
