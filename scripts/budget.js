$(function() { 

// if setting up a new budget:
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
        default:
            alert("Not yet implemented");
    }
    $("#mgmt option[value='none']").prop('selected', true);
});

}); // end page loaded