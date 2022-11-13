/**
 * @fileoverview When cookies are being used as the means for logging in,
 * the login state is determined via getLogin.php; that script stores
 * '#cookiestatus' and '#startpg' on index.php. If login is occurring
 * 'manually' (i.e. without cookies), authenticate.php is invoked via the
 * routine validateUser().
 * 
 * @author Ken Cowles
 * @version 1.0 First release
 */
$('input[name=username]').val(''); // clear login input for page reloads
// globals
const maxFails = 3;
var cookies = navigator.cookieEnabled ? true : false;
var user_cookie_state = $('#cookiestatus').text();
var startpg = $('#startpg').text();
var completed1 = startpg.charAt(0) === '1' ? true : false;
var completed2 = startpg.charAt(1) === '1' ? true : false;
var completed3 = startpg.charAt(2) === '1' ? true : false;

/**
 * This function serves both the getLogin.php cookie access method and the
 * login validateUser() method
 */
 const checkRenewal = () => {
    let msg = "Your password is about to expire:\n" + 
        "You will no longer be able to login unless you renew";
    alert(msg);
    var ans = confirm("Would you like to renew?");
    if (ans) {
        var renewpg = "accounts/renew.php";
        window.open(renewpg, "_self");
    } else {
        alert()
        window.open('index.php', '_self');
    }
};

if (cookies) {
    /**
     * ------ Cookies enabled and cookie exists: getLogin.php ------
     *                  (or session still active)
     */
    if (user_cookie_state === 'NONE' || user_cookie_state === 'MULTIPLE') {
        // a single registration for the cookie's value can't be located in the database
        alert("There is a registration issue; Please re-register");
    } else if (user_cookie_state === 'EXPIRED') {
        alert("Your password has expired;\nPlease re-register for access");
    } else if (user_cookie_state === 'RENEW') {
        checkRenewal(); // full session already established
    } else if (user_cookie_state === 'OK') {
            if (completed1 && completed2 && completed3) {
                var homepg = "main/displayBudget.php";
                window.open(homepg, "_self");
            } else {
                var startpoint = 'edit/newBudgetPanels.php?pnl=' + startpg;
                window.open(startpoint, "_self");
            }
    } 
    // else "NOLOGIN" or unexpected string => do nothing
} else {
    // cookies disabled
    alert("The use of this site requires having cookies enabled on the browser,\n" +
        "even though you may choose to reject the use of personal cookies\n" +
        "When rejected, no cookie information is saved on the browser.");
}

/**
 * ------ No valid cookies discovered, login required ------
 *             validateUser() accomplishes login
 */
var cookie_choice;
var tbl_indx; 
var random;
// To complete login, the user must answer a randomly chosen pre-registered 
// Security Question
$('#submit_answer').on('click', function() {
    let usubmitted = $('#the_answer').val();
    usubmitted = usubmitted.toLowerCase();
    $.post('../accounts/retrieveAnswer.php', {ix: tbl_indx, rx: random}, function(ans) {
        if (usubmitted === ans) {
            alert("You are logged in");
            $('#the_answer').val("");
            var proceed = $.Deferred();
            var ajaxdata = {ix: tbl_indx};
            if (cookie_choice === 'accept') {
                $.ajax({
                    url: "../accounts/sendcookie.php",
                    data: ajaxdata,
                    method: "post",
                    success: function() {
                        proceed.resolve();
                    },
                    error: function() {
                        alert("cookie script failed during login");
                        proceed.reject();
                    }
                });
            } else {
                proceed.resolve();
            }
            $.when(proceed).then(function() {
                $.post('../accounts/login.php', {ix: tbl_indx}, function(status) {
                    if (status === 'OK') {
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
                    } else {
                        alert("System error: login not completed");
                    } 
                });
            });
        } else {
            alert("Your security answer does not match the expected result");
        }
    }, 'text');
});

/**
 * This function takes the login information submitted by the user and determines
 * if the user is registered. Note that the usr_name is not encrypted at this point.
 * 
 * @param {string} usr_name As entered by user on login page
 * @param {string} usr_pass As entered by user on login page
 */
function validateUser(usr_name, usr_pass) {
    $.ajax( {
        url: "accounts/authenticate.php",
        method: "POST",
        data: {'usr_name': usr_name, 'usr_pass': usr_pass},
        dataType: "json",
        success: function(srchResults) {
            var json = srchResults;
            if (json.status === 'LOCATED') {
                startpg = json.start;
                cookie_choice = json.cookies;
                tbl_indx = json.ix;
                $.post('../accounts/retrieveQuestion.php', {ix: tbl_indx}, function(qdat) {
                    $('#the_question').text(qdat.ques);
                    random = qdat.rindx;
                    sec_ques.show();
                }, 'json');
            } else if (json.status === 'EXPIRED') {
                alert("Your password has expired;\nPlease re-register for access");
            } else if (json.status === 'RENEW') {
                checkRenewal(); // full session already established
            } else if (json.status === 'FAIL') { // Something went wrong: multiple entries, bad username/password
                if (json.fail_cnt >= maxFails) {
                    var fails = "There have been " + maxFails + " failed attempts:\n" +
                        "You will need to reset your password";
                    alert(fails);
                    // disable page items
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
                    chg_pass.show();
                } else {
                    var msg = "Invalid credentials, please try again";
                    alert(msg);
                    $('#user').val('');
                    $('#passin').val('');
                }
            } else {
                alert("Something went wrong - please try again!");
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
