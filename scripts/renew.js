/**
 * @fileoverview Allow user to renew account with new password
 * 
 * @author Ken Cowles
 * 
 * @version 2.0 Redesigned login for security improvement
 * @version 2.1 Minor upgrade to success message + password strength check
 */
$(function() {

var spdetails = new bootstrap.Modal(document.getElementById('show_pword_details'));
var security  = new bootstrap.Modal(document.getElementById('security'));
const requiredAnswers = 3;

/**
 * position the registration box on the page
 * 
 * @return {null}
 */ 
 function setbox() {
    let regbox_center = Math.floor($('#container').width()/2);
    let regbox_left = window.innerWidth/2 - regbox_center; // 280 = regbox/2 width
    $('#container').offset({
        top: 140,
        left: regbox_left
    });
    return;
}
setbox();
$(window).resize(setbox);
// adjust the 'container' height based on whether if code is presenrt
if ($('input[name=oldpass]').length > 0) {
    $('#container').css('height', '420px');
}

// Prevent inadvertent form submission via Enter Key
$('document').on('keydown', function(e) {
    if (e.keyCode == 13) {
        e.stopPropagation();
        return false;
    }
});

// page load with 'Show Password' checkbox cleared
$('#ckbox').prop('checked', false);
// toggle visibility of password:
var pword = document.getElementById('pword');
$('#ckbox').on('click', function() {
    if ($(this).is(':checked')) {
        pword.type = "text";
        pword.style.position = "relative";
    } else {
        pword.type = "password";
    }
    return;
});
// display password characteristics 
$('#showdet').on('click', function(ev) {
    ev.preventDefault();
    spdetails.show();
});

/**
 * This function  counts the number of security questions and returns
 * true is correct, false if not
 * 
 * @return {boolean}
 */
const countAns = () => {
    var acnt = 0;
    $('input[id^=q]').each(function() {
        if ($(this).val() !== '') {
            acnt++
        }
    });
    if (acnt > requiredAnswers) {
        alert("You have supplied more than " + requiredAnswers + " answers");
        return false;
    } else if (acnt < requiredAnswers) {
        alert("Please supply answers to " + requiredAnswers + " questions");
        return false;
    } else {
        return true;
    }
}

/**
 * ---- Security Questions ----
 * NOTE: The only time that new answers are required is when a user
 * registration is occurring. Check the value of #new.
 */
var registration = $('#new').text() === 'new' ? true : false;

$('#sq').on('click', function(ev) {
    ev.preventDefault();
    security.show();
});
$('#resetans').on('click', function(ev) {
    ev.preventDefault();
    $('input[name^=ans]').each(function() {
        $(this).val("");
    });
});
$('#closesec').on('click', function(ev) {
    ev.preventDefault();
    if (countAns()) {
        security.hide();
    }
});

// register cookie choice
$('#accept').on('click', function(ev) {
    ev.preventDefault();
    $('#cookie_banner').slideToggle(); 
    $('#usrchoice').val("accept");
    return;
});
$('#reject').on('click', function(ev) {
    ev.preventDefault();
    $('#cookie_banner').slideToggle();
    $('#usrchoice').val("reject");
    return;
});

/**
 * Submit the form 'manually', provide certain data checks
 */
$('#formsubmit').on('click', function(ev) {
    ev.preventDefault();
    $(this).css('background-color', 'gray');
    let submit  = $('input[name=submitter]').val();
    let cookies = $('input[name=cookies]').val();
    let oldpass = $('input[name=oldpass]').length > 0 ? // one-time code present
        $('input[name=oldpass]').val() : '';
    let newpass = $('input[name=password]').val();
    let confirm = $('input[name=confirm]').val();
    let userid  = $('input[name=userid]').val();
    if (userid == '') {
        alert("No user found");
        $(this).css('background-color', '#b47b31');
        return false;
    }
    if (newpass == '') {
        alert("You must fill in a password");
        $(this).css('background-color', '#b47b31');
        return false;
    }
    if ($('#st').css('display') === 'none') {
        alert("You must have a strong password");
        $(this).css('background-color', '#b47b31');
        return false;
    }
    if (confirm !== newpass) {
        alert("Passwords do not match");
        $(this).css('background-color', '#b47b31');
        return false;
    }
    if ($('#usrchoice').val() == 'nochoice') {
        alert("You must designate your cookie choice below");
        $(this).css('background-color', '#b47b31');
        return false;
    }
    // save questions numbers and corresponding answers to security questions
    if (registration) {
        if (!countAns()) {
            $(this).css('background-color', '#b47b31');
            return false;
        }
    }
    let qnos = [];
    let answ = [];
    $('input[id^=q]').each(function() {
        var answer = $(this).val();
        if (answer !== '') {
            let qid = this.id;
            qid = qid.substring(1);
            qnos.push(qid);
            answer = answer.toLowerCase();
            answ.push(answer);
        }
    });
    let ques = qnos.join();
    let uans = answ.join("|"); // in case there's a comma in an answer...
    var ajaxData = {
        submitter: submit,
        userid: userid,
        password: newpass,
        cookies: cookies,
        oldpass: oldpass,
        ques: ques,
        answ: uans
    };
    $.ajax({
        url: 'create_user.php',
        method: "post",
        data: ajaxData,
        dataType: "text",
        success: function(response) {
            $('#formsubmit').css('background-color', '#b47b31');
            if (response == 'NOTFOUND') {
                alert("Could not find the One-Time Code");
            } else if (response === 'DONE') {
                let msg = "Success: Your password has been changed\n";
                msg += "If you accepted cookies you will be logged in auotmatically";
                alert(msg);
                window.open('../index.php', "_self");
            } else {
                alert("Unknown error");
            }
        },
        error: function(_jqXHR, textStatus, errorThrown) {
            var badresult = _jqXHR.textResponse;
            alert("Error encountered: " + textStatus + "; Errno " + errorThrown);
            return false;
        }
    });
});
    
});