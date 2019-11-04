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
var $apays = $('.apday');
var payday = []; // day of the month
var paywith = []; // method of payment
var aname = []; // account name of ap candidate
var $balance = []; // current month's balance (jQ object)
var itemnos =[]; // which entry is affected?
$apays.each(function(indx) {
    if ($(this).text() !== "") {
        itemnos.push(indx);
        $rowtds = $(this).siblings();
        var $paywith = $rowtds.eq(5);
        var $acct = $rowtds.eq(0);
        var $currmo = $rowtds.eq(4);
        payday.push(parseInt($(this).text()));
        paywith.push($paywith.text());
        aname.push($acct.text());
        $balance.push($currmo);
    }
});
// present candidates for autopay to user
var firstap = true;
var nextDef = new $.Deferred();
var payindx = 0;
var payouts = [];
if (payday.length > 0) {
    for (var i=0; i<payday.length; i++) {
        if (payday[i] <= dd) {
            payouts.push(i);
        }
    }
    // payouts[] holds the indices of items to potentially pay
    if (firstap) {
        nextDef.resolve();
        firstap = false;
    }
    // recursion to call all items being paid (identified by payouts[])
    $.when(nextDef).then(function() {
        nextDef = new $.Deferred();
        if (payindx < payouts.length) {
            var ino = payouts[payindx];
            autoPrompt(aname[ino], paywith[ino],
                $balance[ino], itemnos[ino]);
            payindx++;
        } else {
            nextDef.resolve();
        }
    });
}

function autoPrompt(acctname, paymethod, $balanceObj, row_number) {
    var answer = confirm("Do you wish to pay " + acctname +
        " with " + paymethod + "?");
    if (answer) {
            updateAP($balanceObj, row_number);
    } else {
        alert("Payment postponed");
    }
}
// if to be paid
function updateAP($bal, rowno) {
    var old = parseFloat($bal.children().eq(1).text());
    var $obal = $('tr[id=balances]').children().eq(4);
    oldbal = parseFloat($obal.children().eq(1).text());
    var $apwin = $('#ap').detach();
    var apDef = new $.Deferred();
    modal.open({id: 'autopay', height: '68px', width: '220px', 
        content: $apwin, acctbal: old, cbkbal: oldbal, loc: $bal, def: apDef});
    $.when(apDef).then(function() {
        $apwin.appendTo('#allForms');
        nextDef.resolve();
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
 
$('#expense').on('click', function() {
    var exp_form = $('#box').detach();
    modal.open({id: 'expense', width: '340px', height: '160px', content: exp_form});
    $('#allForms').append(exp_form);
});

// go to page corresponding to selected tool
$('#mgmt').on('change', function() {
    var tool = $('#mgmt option:selected').val();
    switch(tool) {
        case "apsetup":
            window.open("../edit/autopay.php", "_self");
            break;
        case "cd_cards":
            window.open("../edit/cardSetup.php", "_self");
            break;
        case "charges":
            window.open("../edit/enterCardData.php", "_self");
        default:
            alert("Not yet implemented");
    }
    $("#mgmt option[value='none']").prop('selected', true);
});

}); // end page loaded