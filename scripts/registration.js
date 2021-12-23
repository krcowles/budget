"use strict"
/**
 * @fileoverview Visually, this script positions the login boxes on the page
 * and toggles password visibility. Functionally, it verifies that all
 * fields in the form have been entered, and registers the member's cookie choice.
 * See scripts/passwordStrength.js for determination of password strength
 * 
 * @author Ken Cowles
 * @version 2.0 Changes for move to Mochahost
 * @version 3.0 Upgraded password/username security
 */
$(function() {   // document ready function

/**
 * position the registration box on the page
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

var spdetails = new bootstrap.Modal(document.getElementById('show_pword_details'));
$('#showdet').on('click', function(ev) {
    ev.preventDefault();
    spdetails.show();
});

// toggle visibility of password:
var cbox = document.getElementsByName('password');
$('#cb').on('click', function() {
    if ($(this).is(':checked')) {
        cbox[0].type = "text";
    } else {
        cbox[0].type = "password";
    }
    return;
});

// registrant's cookie choice:
$('#accept').on('click', function() {
    $('#cookie_banner').hide(); 
    $('#usrchoice').val("accept");
    return;
});
$('#reject').on('click', function() {
    $('#cookie_banner').hide();
    $('#usrchoice').val("reject");
    return;
});

// Prevent inadvertent form submission via enter
$('document').on('keydown', function(e) {
    if (e.keyCode == 13) {
        e.stopPropagation();
        return false;
    }
});

// NOTE: email validation performed by HTML5, and again by server
/**
 * For username problems, notify user immediately
 */
var outstanding_issue = false;
// no spaces in user name:
var nonamespaces = true;
// unique user name:
var goodname = true;
var uniqueness = $.Deferred();
/**
 * Ensure the user name has no embedded spaces
 * @return {null}
 */
var spacesInName = function () {
    var uname = $('#uname').val();
    var res = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    var usrisemail = res.test(uname);
    var problem = false;
    if (uname.indexOf(' ') !== -1) {
        alert("No spaces in user name please");
        problem = true;
    } else if (usrisemail) {
        alert("Do not use email for user name");
        problem = true;
    } else if (uname.length < 6) {
        alert("User names must be at least 6 characters");
        problem = true;
    }else {
        if (goodname) {
            outstanding_issue = false;
        }
    }
    if (problem) {
        $('#uname').focus();
        $('#uname').css('color', 'red');
        nonamespaces = false;
        outstanding_issue = true;
    }
    return;
};
/**
 * Make sure user name is unique;
 * NOTE: TypeScript won't allow a function's return value to be boolean! "you
 * must return a value": hence the return values specified below
 *
 * @return {boolean}
 */
var uniqueuser = function () {
    var data = $('#uname').val();
    var ajaxdata = { username: data };
    var current_users = 'getUsers.php';
    $.ajax(current_users, {
        data: ajaxdata,
        method: 'post',
        success: function (match) {
            if (match === "NO") {
                goodname = true;
            }
            else {
                goodname = false;
            }
            uniqueness.resolve();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            uniqueness.reject();
            var newDoc = document.open();
            newDoc.write(jqXHR.responseText);
            newDoc.close();
        }
    });
};
$('#uname').on('change', function () {
    spacesInName();
    if (nonamespaces) {
        uniqueuser();
        $.when(uniqueness).then(function () {
            if (!goodname) {
                alert("This user name is already taken");
                $('#uname').css('color', 'red');
                outstanding_issue = true;
            }
            else {
                if (nonamespaces) {
                    outstanding_issue = false;
                }
            }
            uniqueness = $.Deferred(); // re-establish for next event
        });
    }
});
$('#uname').on('focus', function () {
    $(this).css('color', 'black');
});
// input fields: no blanks; no username spaces; valid email address
$("form").on('submit', function (ev) {
    ev.preventDefault();
    if (!submittable) {
        alert("You must enter an acceptable password");
        return false;
    }
    if (outstanding_issue) {
        alert("Please correct item(s) in red before submitting");
        return false;
    }
    var allinputs = document.getElementsByClassName('signup');
    for (var i = 0; i < allinputs.length; i++) {
        var inputbox = allinputs[i];
        if (inputbox.value == '') {
            alert("Please complete all entries");
            return false;
        }
    }
    if ($('#cookie_banner').css('display') !== 'none') {
        alert("Please accept or reject cookis");
        return false;
    }
    // check for a valid email address:
    let usremail = $('input[name=email]').val();
    let regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (!regex.test(usremail)) {
        alert("Please enter a valid email address");
        $('input[name=username]').val("");
        return false;
    }
    let cookies  = $('input[name=cookies]').val();
    let username = $('input[name=username]').val();
    let password = $('input[name=password]').val();
    var ajaxdata = {submitter: 'create', username: username, password: password,
        cookies: cookies, email: usremail};
    $.ajax({
        url: 'create_user.php',
        method: 'post',
        dataType: 'text',
        data: ajaxdata,
        success: function(response) {
            if (response == 'DONE') {
                window.open('../index.php', '_self');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("Unsuccessful: " + textStatus + "; " + errorThrown);
        }
    });
});

});