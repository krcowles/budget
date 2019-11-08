$(function() { 

var today = new Date();
var dd = parseInt(String(today.getDate()).padStart(2, '0'));
var mm = parseInt(String(today.getMonth() + 1).padStart(2, '0')); //January is 0!
var yyyy = parseInt(today.getFullYear());

// if no budget data:
if (setup) {
    window.open("../edit/newBudget.html", "_self");
}
// if no credit card data:
if ($('#chgaccts').text() == '0') {
    var ans = confirm("There is no credit/debit card data\n" + 
        "Do you wish to set that up now?");
    if (ans) {
        window.open("../edit/cardSetup.php?num=1", "_self");
    } else {
        var cc = confirm("I don't want to be reminded again\n" +
            "(Use 'Account Management Tools' to change");
        if (cc) {
            window.open("../edit/cardSetup.php?num=0", "_self");
        }
    }
}

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
// user presentation of autopy candidates:
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

// account listing in select box
var acct_list = [];
var acct_select_box = '<select id="selacct">\n';
 $('.acct').each(function() {
    var aval = $(this).text();
    acct_list.push(aval);
    acct_select_box += '<option value="' + aval + '">' + aval + '</option>\n';
 });
 acct_select_box += '</select>\n';
 $('#modal_accts').after(acct_select_box);
 
 /**
  * Button redirects
  */
$('#expense').on('click', function() {
    var exp_form = $('#box').detach();
    modal.open({id: 'expense', width: '340px', height: '160px', content: exp_form});
    $('#allForms').append(exp_form);
});
$('#income').on('click', function() {
    window.open("../utilities/enterIncome.php", "_self");
});
$('#deposit').on('click', function() {
    alert("Under Construction");
});
$('#recon').on('click', function() {
    window.open("../utilities/reconcile.php", "_self");
});
$('#movefunds').on('click', function() {
    alert("Under Construction");
});

// go to page corresponding to selected tool
$('#mgmt').on('change', function() {
    var tool = $('#mgmt option:selected').val();
    switch(tool) {
        case "charges":
            window.open("../edit/enterCardData.php", "_self");
            break;
        case "apsetup":
            window.open("../edit/autopay.php", "_self");
            break;
        case "cd_cards":
            window.open("../edit/cardSetup.php", "_self");
            break;
        case "renameacct" :
        case "addacct" :
        case "delacct" :
        case "mvacct" :
        default:
            alert("Not yet implemented");
    }
    $("#mgmt option[value='none']").prop('selected', true);
});

}); // end page loaded