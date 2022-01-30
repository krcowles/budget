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

/**
 * ---- Username validation: notify user as soon as entered ----
 */
var valid_username = false;
 /**
  * Ensure the username has no embedded spaces, is min length
  * and not a duplicate of an existing username
  * @return {null}
  */
$('#uname').on('change', function () { // allows immediate feedback
    var checkDef = $.Deferred();
    var uname = $('#uname').val();
    if (uname.indexOf(' ') !== -1) {
        alert("No spaces in user name please");
        valid_username = false;
    } else if (uname.length < 6) {
        alert("User names must be at least 6 characters");
        valid_username = false;
    } else {
        valid_username = true;
        $('#uname').focus();
        $('#uname').css('color', 'black');
    }
    $.ajax({
        url: '../accounts/getDups.php',
        method: 'post',
        data: {username: uname},
        dataType: 'text',
        success: function(match) {
            if (match === "YES") {
                valid_username = false;
                alert("Please select a different user name");
            }
            checkDef.resolve(); 
        },
        error: function() {
            alert("Error encountered checking username");
            valid_username = false;
            checkDef.resolve();
        }   
    });
    $.when(checkDef).then(function() {
        if (!valid_username) {
            $('#uname').focus();
            $('#uname').css('color', 'red');
        }
    }); 
    return;
 });
 
/**
 * ---- Email validation: validated when entry is completed, and again via html form checks ----
 */
var valid_email = false;
$('#email').on('change', function() {
    var checkDef = $.Deferred();
    let msg = '';
    let uemail = $(this).val();
    valid_email = email_test_pattern.test(uemail);
    if (valid_email) {
        var adata = {email: uemail};
        $.post('getDups.php', adata, function(match) {
            if (match === 'YES') {
                msg = "Cannot complete request with this email";
                valid_email = false;
            }
            checkDef.resolve();
        });
    } else {
        msg = "Please enter a valid email address";
        checkDef.resolve();
    }
    $.when(checkDef).then(function() {
        if (!valid_email) {
            alert(msg);
            $('#email').focus();
            $('#email').css('color', 'red');
        } else {
            $('#email').css('color', 'black');
        }
    });
});

/**
 * Final checks when form is submitted
 */
$("form").on('submit', function (ev) {
    ev.preventDefault();
    $('#submit').css('background-color', 'gray');
    var allinputs = document.getElementsByClassName('signup');
    for (var i = 0; i < allinputs.length; i++) {
        var inputbox = allinputs[i];
        if (inputbox.value == '') {
            alert("Please complete all entries");
            return false;
        }
    }
    if (!valid_username || !valid_email) {
        alert("Please correct item(s) in red before submitting");
        return false;
    }
 
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
                        $('#submit').css('background-color', '#b47b31');
                        if (result === 'ok') {
                            alert("An email has been sent");
                        } else if (result === 'bad') {
                            alert("Email was not valid");
                        } else if (result === 'nofind') {
                            alert("Could not find registration:\n" +
                                "Please try again");
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        alert("FAILED: " + textStatus);
                    }
                });
            } else if (response === 'bademail') {
                alert("The email you sent was not valid");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("Unsuccessful: " + textStatus + "; " + errorThrown);
        }
    });
});

});