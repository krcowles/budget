$(function() { 

var today = new Date();
var dd = parseInt(String(today.getDate()).padStart(2, '0'));
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January was otherwise 0!
var yyyy = parseInt(today.getFullYear());
var user = $('#user').text();
if (user === 'krc') {
    $('#admin').css('display', 'block');
}
$('#admin').on('click', function() {
    window.open('../admin/admintools.php', "_blank");
});

// check autopayment status
var payday = [];
var paywith = []; // method of payment
var aname = []; // account name of autopay candidate
var rowno = []; // budget rowno in which AP occurs
var ap_candidates = false;
// get all autopays having dates; find those that are due
$('.apday').each(function(indx) {
    if ($(this).text() !== "") {
        var apday = parseInt($(this).text());
        if (apday <= dd) {
            $rowtds = $(this).siblings();
            var pd = $rowtds.eq(6).text().trim();
            if (pd !== mm) {
                rowno.push(indx);
                var $paywith = $rowtds.eq(5);
                var $acct = $rowtds.eq(0);
                payday.push(parseInt($(this).text()));
                paywith.push($paywith.text().trim());
                aname.push($acct.text());
                ap_candidates = true;
            }
        }
    }
});
// user presentation of autopay candidates:
if (ap_candidates) {
    var $userlist = $('table#modal_table tbody');
    // create the html list based on number of autopays due
    for (var j=0; j<aname.length; j++) {
        var item_html = '<tr><td><div class="tdht">' + aname[j] + '<br />Due day: ' +
            payday[j] + '&nbsp;&nbsp;Payment $ <input id="amt' + j +
            '" type="text" /><br />Payee: <input id="payee' + j +
            '" type="text" /></div></td>';
        item_html += '<td><div class="tdht"><button class="modal_button" ' +
            'id="paymt' + j + '">Pay this</button><br />' +
            '&nbsp;&nbsp;using: ' + paywith[j] +  '</div></td></tr>';
        $userlist.append(item_html);
    }
    // table is created, now present data to modals.js
    var ap_object = $('#ap').detach();
    var def = new $.Deferred(); // only one can pay at a time, so one deferred
    modal.open({
        id: 'autopay', height: '200px', width: '390px', content: ap_object,
        user: user, method: paywith, acct_name: aname, row_no: rowno,
        deferred: def
    });
    $.when( def ).then(function() {
        $('#allForms').append(ap_object);
    });
}

// disable Check/Draft option in autopay select box
var $apcdsel = $('#ccselap .allsel');
$apcdsel[0].options[0].disabled = 'disabled';
// disable first option in Delete Card box
var $moddc = $('#deletecard .allsel');
$moddc[0].options[0].disabled = 'disabled';
// Add option for moving account
var $mvlist = $('#mvto .partsel');
var undisopt = document.createElement('option');
undisopt.text = "Undistributed Funds";
$mvlist[0].add(undisopt);
// disable Undistribute Funds for renaming accounts:
var $ren = $('#asel .fullsel');
for (var j=0; j<$ren[0].options.length; j++) {
    if ($ren[0].options[j].text == 'Undistributed Funds') {
        $ren[0].options[j].disabled = 'disabled';
        break;
    }
}

}); // end page loaded