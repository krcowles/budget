/**
 * @fileoverview Check total password strength as it is entered by the registrant.
 * If the length and other criteria are not met, do not allow submission. Minimum length
 * is 11 char and each of the four conditions listed must be met.
 * 
 * @author Ken Cowles
 * @version 1.0 First pass checker
 */
var lcalpha = /[a-z]/;
var ucalpha = /[A-Z]/;
var numchar = /[0-9]/;
var spcchar = /\W|_/;
var lc = 0;
var uc = 0;
var nm = 0;
var sp = 0;
var total = 0;
var focus = false;
var current_password = '';
$('.signup').val('');

const addKey = (type, key) => {
    let cnt = 0;
    switch(type) {
        case 'lc':
            lc++;
            cnt = lc;
            break;
        case 'uc':
            uc++;
            cnt = uc;
            break;
        case 'nm':
            nm++;
            cnt = nm;
            break;
        case 'sp':
            sp++;
            cnt = sp;
    }
    let id = "#" + type;
    $(id).text(cnt);
    $(id).css('color', 'darkgreen');
    total++;
    $('#total').text(total);
    current_password += key;
    let $pbox = $('#pword');
    if (total >= 11 && lc > 0 && uc > 0 && nm > 0 && sp > 0) {
        if ($pbox.hasClass('weak')) {
            $pbox.removeClass('weak');
            $pbox.addClass('strong');
            $('#wk').hide();
            $('#st').show();
            $('#showdet').css('display', 'none');
        }
    }
};
const keyChecker = (ev) => {
    let thiskey = ev.key;
    if (thiskey !== "Shift") {
        if (thiskey === "Backspace") {
            current_password = current_password.slice(0, -1);    
        } else if (thiskey.length === 1) {
            if (lcalpha.test(thiskey)) {
                addKey('lc', thiskey);
            } else if (ucalpha.test(thiskey)) {
                addKey('uc', thiskey);
            } else if (numchar.test(thiskey)) {
                addKey('nm', thiskey);
            } else if (spcchar.test(thiskey)) {
                addKey('sp', thiskey);
            }
        } 
    }
    return;
};
$('#pword').on('focus', function() {
    focus = true;
    document.addEventListener('keydown', keyChecker);
    return;

});
$('#pword').on('blur', function() {
    if (focus) {
        document.removeEventListener('keydown', keyChecker);
    }
    focus = false;
    return;
});
