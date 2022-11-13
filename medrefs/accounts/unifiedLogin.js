"use strict";
/**
 * @fileoverview Adjust page according to form type
 *
 * @author Ken Cowles
 *
 * @version 1.0 First MedRefs release
 */
$(function () {
    var cookie_modal = new bootstrap.Modal(document.getElementById('cooky'), {
        keyboard: false
    });
    var reg = isMobile && !isTablet ? { height: 260 } : { height: 380 };
    var log = isMobile && !isTablet ? { height: 260 } : { height: 290 };
    var pwd = isMobile && !isTablet ? { height: 400 } : { height: 420 };
    var formtype = $('#formtype').text();
    var $container = $('#container');
    /**
     * The code executed depends on which formtype is in play
     */
    switch (formtype) {
        case 'reg':
            // clear inputs on page load/reload 
            $('#fname').val("");
            $('#lname').val("");
            $('#uname').val("");
            $('#umail').val("");
            $container.css({
                height: reg.height
            });
            $('#policylnk').on('click', function () {
                var plnk = '../accounts/PrivacyPolicy.pdf';
                window.open(plnk, '_blank');
            });
            $('#formsubmit').css('top', '40px');
            /**
             * For username problems, or duplicate email, notify user immediately;
             *  NOTE: email validation is performed by HTML5, and again by server
             */
            var dup_email = false;
            var space_in_name = false;
            var dup_name = false;
            var min_length = true;
            /**
             * Check for duplicate email
             */
            var duplicateEmailCheck = function () {
                var umail = $('#umail').val();
                var ajaxdata = { email: umail };
                var dupcheck = '../accounts/dupCheck.php';
                $.ajax({
                    url: dupcheck,
                    data: ajaxdata,
                    method: "post",
                    dataType: "text",
                    success: function (match) {
                        if (match === "NO") {
                            dup_email = false;
                            $('#umail').css('color', 'black');
                        }
                        else {
                            // inputs of type 'email' cannot have their color altered!
                            dup_email = true;
                            var rmail = 'ALREADY_IN_USE: ' + umail;
                            $('#umail').val(rmail);
                            alert("This email is already in use");
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        dup_email = true;
                        $('#umail').css('color', 'red');
                        if (appMode === 'development') {
                            var newDoc = document.open();
                            newDoc.write(jqXHR.responseText);
                            newDoc.close();
                        }
                        else { // production
                            var msg = "An error has occurred: " +
                                "We apologize for any inconvenience\n" +
                                "The webmaster has been notified; please try again later";
                            alert(msg);
                            var ajaxerr = "Trying to access dupCheck; Error text: " +
                                textStatus + "; Error: " + errorThrown;
                            var errobj = { err: ajaxerr };
                            $.post('../php/ajaxError.php', errobj);
                        }
                    }
                });
                return;
            };
            $('#umail').on('blur', function () {
                duplicateEmailCheck();
            });
            /**
             * Ensure the user name has no embedded spaces
             */
            var spacesInName = function () {
                var uname = $('#uname').val();
                if (uname.indexOf(' ') !== -1) {
                    alert("No spaces in user name please");
                    $('#uname').css('color', 'red');
                    space_in_name = true;
                }
                else {
                    space_in_name = false;
                    $('#uname').css('color', 'black');
                }
                return;
            };
            /**
             * Make sure user name is unique;
             * NOTE: TypeScript won't allow a function's return value to be boolean!
             * This function will set a global, goodname, instead.
             */
            var uniqueUserCheck = function () {
                var data = $('#uname').val();
                var ajaxdata = { username: data };
                var dupCheck = '../accounts/dupCheck.php';
                $.ajax({
                    url: dupCheck,
                    data: ajaxdata,
                    method: 'post',
                    success: function (match) {
                        if (match === "NO") {
                            dup_name = false;
                            $('#uname').css('color', 'black');
                        }
                        else {
                            dup_name = true;
                            $('#uname').css('color', 'red');
                            alert("Please select another user name");
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        if (appMode === 'development') {
                            var newDoc = document.open();
                            newDoc.write(jqXHR.responseText);
                            newDoc.close();
                        }
                        else { // production
                            dup_name = true;
                            $('#uname').css('color', 'red');
                            var msg = "An error has occurred:  " +
                                "We apologize for any inconvenience\n" +
                                "The webmaster has been notified; please try again later";
                            alert(msg);
                            var ajaxerr = "Trying to get Users list; Error text: " +
                                textStatus + "; Error: " + errorThrown;
                            var errobj = { err: ajaxerr };
                            $.post('../php/ajaxError.php', errobj);
                        }
                    }
                });
                return;
            };
            $('#uname').on('blur', function () {
                spacesInName();
                if (!space_in_name) {
                    uniqueUserCheck();
                    var name_1 = $('#uname').val();
                    if (name_1.length > 0 && name_1.length < 6) {
                        min_length = false;
                        alert("You must choose a username with at least 6 characters");
                    }
                    else {
                        min_length = true;
                    }
                }
            });
            // input fields: no blanks; no username spaces; valid email address;
            // no other faults
            $("#form").on('submit', function (ev) {
                ev.preventDefault();
                if (dup_name || space_in_name || dup_email || !min_length) {
                    alert("Cannot proceed until all entries are corrected");
                    return false;
                }
                $('#formsubmit').css('background-color', '#325d81');
                $('#formsubmit').css('color', 'white');
                var formdata = $('#form').serializeArray();
                var proposed_name = formdata[3]['value'];
                var proposed_email = formdata[4]['value'];
                msg_server_err = "Server error: cleanup Users table\n" +
                    "Registrant: " + proposed_name + "; email " + proposed_email;
                $.ajax({
                    url: 'create_user.php',
                    data: formdata,
                    dataType: 'text',
                    method: 'post',
                    success: function (result) {
                        if (result === 'bademail') {
                            var err = "Your registration could not be completed\n" +
                                "The submitted email is not valid";
                            alert(err);
                        }
                        else if (result === "DONE") {
                            msg_success = "Please wait for an email to complete registration";
                            msg_admin = "User email not sent, but entry has been created in Users:\n" +
                                "Registrant: " + proposed_name + "; email: " + proposed_email;
                            msg_server_err = "Server error: cleanup Users table\n" +
                                "Registrant: " + proposed_name + "; email: " + proposed_email;
                            var email = $('#umail').val();
                            sendEmailLink(true, email);
                        }
                    },
                    error: function (jqXHR) {
                        var newDoc = document.open();
                        newDoc.write(jqXHR.responseText);
                        newDoc.close();
                    }
                });
                return true;
            });
            break;
        case 'pwd_reset': // renew => anytime password is changed, e.g. 'Forgot Password', etc.
            cookie_modal.show();
            var accept_btn = '#accept';
            var reject_btn = '#reject';
            // default cookie choice:
            $('#usrchoice').val("reject");
            // declared cookie choice:
            $(accept_btn).on('click', function () {
                $('#usrchoice').val("accept");
                cookie_modal.hide();
            });
            $(reject_btn).on('click', function () {
                $('#usrchoice').val("reject");
                cookie_modal.hide();
            });
            // clear inputs on page load/reload
            $('#password').val("");
            $('#confirm').val("");
            $('#ckbox').prop('checked', false);
            var ix = $('#ix').text();
            tbl_indx = ix; // required in validateUser's #closesec function
            var pdet = new bootstrap.Modal(document.getElementById('show_pword_details'));
            var cban = new bootstrap.Modal(document.getElementById('cooky'));
            if (mobile) {
                cban.show();
            }
            var tmp_pass = $('#one-time').val();
            $container.css({
                height: pwd.height
            });
            $('#formsubmit').css('top', '40px');
            /**
             * Populate the security questions with the user's answers, as
             * he/she may not review them prior to submitting, and they need
             * to be present for the answer check in 'formsubmit'.
             */
            $.post('usersQandA.php', { ix: ix }, function (contents) {
                $('#uques').empty();
                $('#uques').append(contents);
            });
            // toggle visibility of password:
            var cbox = document.getElementsByName('password');
            $('#ckbox').on('click', function () {
                if ($(this).is(':checked')) {
                    cbox[0].setAttribute("type", "text");
                }
                else {
                    cbox[0].setAttribute("type", "password");
                }
            });
            // show details of password when 'weak'
            $('#showdet').on('click', function (ev) {
                ev.preventDefault();
                pdet.show();
            });
            // security modal buttons operation spec'd in validateUser.js
            $('#rvw').on('click', function (ev) {
                ev.preventDefault();
                updates.show();
            });
            // SUBMIT FORM
            $('#formsubmit').on('click', function (ev) {
                ev.preventDefault();
                // NOTE: Applying Security Questions updates the database
                if ($('#st').css('display') === 'none') {
                    alert("You must use a strong password");
                    return false;
                }
                var password = $('input[name=password]').val();
                if (password === '') {
                    alert("You have not entered a passwsord");
                    return false;
                }
                var cookies = $('#usrchoice').val();
                if (cookies === 'nochoice') {
                    alert("Please accept or reject cookies");
                    return false;
                }
                var confirm = $('#confirm').val();
                if (confirm === '') {
                    alert("You must confirm your password");
                    return false;
                }
                else if (confirm !== password) {
                    alert("Your passwords do not match");
                    return false;
                }
                var acnt = 0;
                $('input[id^=q]').each(function () {
                    if ($(this).val() !== '') {
                        acnt++;
                    }
                });
                if (acnt !== 3) {
                    alert("You must supply exactly 3 answers to security questions");
                    return false;
                }
                $('#formsubmit').hide();
                var formdata = {
                    submitter: 'change',
                    code: tmp_pass,
                    password: password,
                    cookies: cookies,
                    userid: ix
                };
                $.ajax({
                    url: 'create_user.php',
                    method: 'post',
                    data: formdata,
                    dataType: 'text',
                    success: function (result) {
                        if (result === 'DONE') {
                            alert("Your password has been updated\nAnd you are logged in");
                            window.open('../pages/main.php', '_self');
                        }
                        else if (result === 'NOTFOUND') {
                            alert("Your one-time code was not located\n" +
                                "Please try again by entering the code in your email\n" +
                                "into the 'One-time code' box");
                            return;
                        } else {
                            alert("Unexpected error has occurred - admin has been notified");
                            var ajaxerr = "Server error in create_user: cleanup USERS\n" +
                                "registrant" + proposed_name + "; email " +
                                proposed_email + " result: " + result;
                            var errobj = { err: ajaxerr };
                            $.post('../errors/ajaxError.php', errobj);
                            return;
                        }
                    },
                    error: function (jqXHR) {
                        var newDoc = document.open();
                        newDoc.write(jqXHR.responseText);
                        newDoc.close();
                    }
                });
                return true;
            });
            break;
        case 'log':
            $container.css({
                height: log.height
            });
            $('#formsubmit').css('top', '50px');
            $('#send').on('click', function() {
                var umail = $('#rstmail').val();
                if (umail == '') {
                    alert("You must enter a valid email address");
                    return false;
                }
                // sendmail will check for valid email protocol
                msg_success = "You will receive an email with a link to reset " +
                    "your password";
                msg_admin = "Login failure for email user: " + umail;
                msg_server_err = "Login server failure for email user: " + umail;
                sendEmailLink(false, umail);
                return;
            });
            $('#form').on('submit', function (ev) {
                ev.preventDefault();
                var user = $('#username').val();
                var pass = $('#password').val();
                if (user === '' || pass === '') {
                    alert("Both username and password must be specified");
                    return false;
                }
                validateUser(user, pass);
                var nothing = document.getElementById("password");
                nothing.focus();
                return;
            });
            break;
    }
});
