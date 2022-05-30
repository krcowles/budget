"use strict"
/**
 * @fileoverview Visually, this script positions the login boxes on the page
 * and toggles password visibility. Functionally, it verifies that all
 * fields in the form have been entered, and registers the member's security
 * questions and cookie choice. See scripts/passwordStrength.js for determination
 * of password strength
 * 
 * @author Ken Cowles
 * @version 2.0 Changes for move to Mochahost
 * @version 3.0 Upgraded password/username security; simplified verification process
 */
$(function() {   // document ready function

/**
 * Function to position the registration box on the page
 * 
 * @return {null}
 */ 
function setbox() {
    let regbox_center = Math.floor($('#registration').width()/2);
    let regbox_left = window.innerWidth/2 - regbox_center; // 280 = regbox/2 width
    $('#registration').offset({
        top: 160,
        left: regbox_left
    });
    return;
}
setbox();
$(window).on('resize', setbox);

const email_test_pattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
var email_status;
var uname_status;

// Prevent inadvertent form submission via Enter Key
$('document').on('keydown', function(e) {
    if (e.keyCode == 13) {
        e.stopPropagation();
        return false;
    }
});

// clear inputs on reload:
$('#uname').val("");
$('#email').val("");

// when input text turns red (due to error), focus on input returns to black text
$('#uname').focus(function() {
    $(this).css('color', 'black');
});
$('#email').focus(function() {
    $(this).css('color', 'black');
});
/**
 * ---- Username validation ----
 *
 * Ensure the username has no embedded spaces, is min length
 * and not a duplicate of an existing username
 */
const validateUser = (deferred) => {
    var uname = $('#uname').val();
    if (uname.indexOf(' ') !== -1) {
        uname_status = "No spaces in user name please";
        deferred.resolve();
        return;
    } else if (uname.length < 6) {
        uname_status = "User names must be at least 6 characters";
        deferred.resolve();
        return;
    }
    $.ajax({
        url: '../accounts/getDups.php',
        method: 'post',
        data: {username: uname},
        dataType: 'text',
        success: function(match) {
            if (match === "YES") {
                uname_status = "Please select a different user name";
            }
            deferred.resolve(); 
        },
        error: function() {
            uname_status = "Error encountered checking username";
            deferred.resolve();
        }   
    });
    return;
};
 
/**
 * ---- Email validation ----
 * 
 * email meets criterion specified for emails; no duplicate emails allowed
 */
const validateEmail = (deferred) => {
    let uemail = $('#email').val();
    let valid_email = email_test_pattern.test(uemail);
    if (valid_email) {
        var adata = {email: uemail};
        $.post('getDups.php', adata, function(match) {
            if (match === 'YES') {
                email_status = "Cannot complete request with this email";
            }
            deferred.resolve();
        });
    } else {
        email_status = "Please enter a valid email address";
        deferred.resolve();
    }
};

/**
 * Final checks when form is submitted
 */
$("form").on('submit', function (ev) {
    ev.preventDefault();
    var allinputs = document.getElementsByClassName('signup');
    for (var i = 0; i < allinputs.length; i++) {
        var inputbox = allinputs[i];
        if (inputbox.value == '') {
            alert("Please complete all entries");
            return false;
        }
    }
    let usermsg = '';
    let emailDeferred = $.Deferred();
    let unameDeferred = $.Deferred();
    uname_status = '';
    email_status = '';
    validateUser(unameDeferred);
    validateEmail(emailDeferred);
    $.when( unameDeferred, emailDeferred ).then(function() {
        if (uname_status !== '') {
            usermsg += uname_status + "\n";
            $('#uname').css('color', 'red');
        }
        if (email_status !== '') {
            usermsg += email_status;
            $('#email').css('color', 'red');
        }
        if (usermsg !== '') {
            alert(usermsg);
            return false;
        } else {
            $('#submit').css('background-color', 'gray');
            $('#pending').show();
            let usremail = $('#email').val();
            usremail = usremail.toLowerCase();
            let username = $('input[name=username]').val();
            let newreg   = 'y';
            var ajaxdata = {submitter: 'create', username: username, email: usremail};
            $.ajax({
                url: 'create_user.php',
                method: 'post',
                dataType: 'text',
                data: ajaxdata,
                success: function(response) {
                    if (response === 'DONE') {
                        // Send an email to registrant with security code & link
                        $.ajax({
                            url: '../accounts/sendmail.php',
                            method:'post',
                            data:{newreg: newreg, email: usremail},
                            success: function(result) {
                                $('#pending').hide();
                                if (result === 'ok') {
                                    $('#submit').prop('disabled', true);
                                    alert("An email has been sent");
                                } else if (result === 'bad') {
                                    $('#submit').css('background-color', '#b47b31');
                                    alert("Email was not valid");
                                } else if (result === 'nofind') {
                                    $('#submit').css('background-color', '#b47b31');
                                    alert("Could not find registration:\n" +
                                        "Please try again");
                                }
                            },
                            error: function(jqXHR, textStatus) {
                                $('#pending').hide();
                                $('#submit').css('background-color', '#b47b31');
                                alert("Failed to send email: " + textStatus);
                            }
                        });
                    } else if (response === 'bademail') {
                        $('#pending').hide();
                        $('#submit').css('background-color', '#b47b31');
                        alert("The email you sent was not valid");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#pending').hide();
                    $('#submit').css('background-color', '#b47b31');
                    alert("Unsuccessful: " + textStatus + "; " + errorThrown);
                }
            });
        }
    });
});
           
});
