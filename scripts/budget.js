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

/*
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
        case "delacct" :
            $('#delacct').after($fromlist);
            var deleter = $('#del').detach();
            modal.open({id: 'delacct', height: '186px', width: '340px',
                content: deleter});
            $('#allForms').append(adder);
            break;
        case "mvacct" :
            $('#mvfrom').after($fromlist);
            $('#mvto').after($tolist);
            var mover = $('#mv').detach();
            modal.open({id: 'mvacct', height: '240px', width: '340px',
                content: mover});
            $('#allForms').append(mover);
    }
    $("#mgmt option[value='none']").prop('selected', true);
});
*/
}); // end page loaded