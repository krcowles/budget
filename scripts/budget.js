$(function() { 

var today = new Date();
var dd = parseInt(String(today.getDate()).padStart(2, '0'));
var mm = parseInt(String(today.getMonth() + 1).padStart(2, '0')); //January is 0!
var yyyy = parseInt(today.getFullYear());

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
            if (pd === 'N') {
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
            'id="paymt' + j + '">Pay this</button></div></td></tr>';
        $userlist.append(item_html);
    }
    var ap_object = $('#ap').detach();
    modal.open({
        id: 'autopay', height: '300px', width: '500px', content: ap_object,
        day: payday, method: paywith, acct_name: aname, row_no: rowno, 
    });
}

// disable first option in Delete Card box
var $moddc = $('#deletecard .allsel');
$moddc[0].options[0].disabled = 'disabled';
// Add option for moving account
var $mvlist = $('#mvto .partsel');
var undisopt = document.createElement('option');
undisopt.text = "Undistributed Funds";
$mvlist[0].add(undisopt);

}); // end page loaded