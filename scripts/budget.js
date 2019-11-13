$(function() { 

var today = new Date();
var dd = parseInt(String(today.getDate()).padStart(2, '0'));
var mm = parseInt(String(today.getMonth() + 1).padStart(2, '0')); //January is 0!
var yyyy = parseInt(today.getFullYear());

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
        } else {
            window.open("../main/budget.php");
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
var secondary = '<select id="second">\n';
var tempacct = '<option value="Temporary Accounts" disabled>Temporary Accounts</option>\n';
$('.acct').each(function() {
var aval = $(this).text();
acct_list.push(aval);
if (aval === "Temporary Accounts") {
    acct_select_box += tempacct;
    secondary += tempacct;
} else {
    acct_select_box += '<option value="' + aval + '">' + aval + '</option>\n';
    secondary += '<option value="' + aval + '">' + aval + '</option>\n';
}
});
acct_select_box += '</select><br />';
secondary += '</select><br />';
$('#modal_accts').after(acct_select_box);
var rawaccts = [];
for (var t=0; t<acct_list.length; t++) {
    if (acct_list[t] == 'Undistributed Funds') {
        break;
    } else {
        rawaccts.push(acct_list[t]);
    }
}
var $fromlist = $('<select id="fromlist"></select>');
var $tolist   = $('<select id="tolist"></select');
// allow selecting only items prior to 'Undistributed Funds'
$.each(rawaccts, function (i, item) {
    $fromlist.append($('<option>', { 
        value: item,
        text : item
    }));
});
$.each(rawaccts, function (i, item) {
    $tolist.append($('<option>', { 
        value: item,
        text : item
    }));
});
 
 /**
  * Button redirects
  */
$('#expense').on('click', function() {
    var exp_form = $('#box').detach();
    modal.open({id: 'expense', width: '342px', height: '220px', content: exp_form});
    $('#allForms').append(exp_form);
});
$('#income').on('click', function() {
    var income_form = $('#distinc').detach();
    modal.open({id: 'income', height: '280px', width: '320px', content: income_form});
    $('#allForms').append(income_form);
});
$('#deposit').on('click', function() {
    var funds = $('#dep').detach();
    modal.open({id: 'deposit', height: '170px', width: '220px', content: funds});
    $('#allForms').append(funds);
});
$('#recon').on('click', function() {
    window.open("../utilities/reconcile.php", "_self");
});
$('#movefunds').on('click', function() {
    $('#xfrfrom').after(acct_select_box);
    $('#xfrto').after(secondary);
    var xfr = $('#xfr').detach();
    modal.open({id: 'xfr', height: '234px', width: '240px', content: xfr});
    $('#allForms').append(xfr);
});

/*
 * Redirects from selection in "Account Management Tools"
 */
$('#mgmt').on('change', function() {
    var tool = $('#mgmt option:selected').val();
    switch(tool) {
        case "edaccts":
            window.open("../edit/newBudget.html", "_self");
            break;
        case "charges":
            window.open("../edit/editCreditCharges.php", "_self");
            break;
        case "apsetup":
            window.open("../edit/autopay.php", "_self");
            break;
        case "cd_cards":
            window.open("../edit/cardSetup.php?num=1", "_self");
            break;
        case "renameacct" :
            $('#asel').after(acct_select_box);
            var namer = $('#rename').detach();
            modal.open({id: 'rename', width: '320px', height: '140px',
            content: namer});
            $('allForms').append(namer);
            break;
        case "addacct" :
            var adder = $('#addacct').detach();
            modal.open({id: 'addacct', width: '360px', height: '346px',
                content: adder});
                $('#allForms').append(adder);
            break;
        case "delacct" :
            $('#delacct').after($fromlist);
            var deleter = $('#del').detach();
            modal.open({id: 'delacct', height: '186px', width: '340px', content: deleter});
            $('#allForms').append(adder);
            break;
        case "mvacct" :
            $('#mvfrom').after($fromlist);
            $('#mvto').after($tolist);
            var mover = $('#mv').detach();
            modal.open({id: 'mvacct', height: '240px', width: '340px', content: mover});
            $('#allForms').append(mover);
    }
    $("#mgmt option[value='none']").prop('selected', true);
});

}); // end page loaded