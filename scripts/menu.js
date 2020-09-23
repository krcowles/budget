/**
 * @fileoverview There are two sections within: menu visuals and operation;
 *               menu item selection and execution
 * @author Ken Cowles
 * @version 2.0 Secure login
 */
$(function() { // doc ready statement
/**
 * This section places items on the page, sizing them and operating open/close arrows
 */
// set main menu widths (determined by trial and error for appearance; not derived - yet)
var menuwidths = ['96', '84', '70', '60', '72',    '82', '83', '91', '98', '84','64'];
var $buds = $('.bud a');
$buds.each(function(indx) {
    $(this).width(menuwidths[indx]);
    return;
});
// user can revisit cookie choice under help menu
var choice = $('#usercookies').text();
$('#chglink').text(choice);

/**
 * Sets the menu arrow - open
 * @param {jQueryNode} sub The jQuery subroutine node
 * 
 * @return {null}
 */
const showOpenSymbol = (sub) => {
    let arrow = sub.find('.dropdown');
    arrow.removeClass('menu-close');
    arrow.addClass('menu-open');
    return;
};
/**
 * Sets the menu arrow - close
 * @param {jQueryNode} sub The jQuery subroutine node
 * 
 * @return {null}
 */
const showCloseSymbol = (sub) => {
    let arrow = sub.find('.dropdown');
    arrow.removeClass('menu-open');
    arrow.addClass('menu-close');
    return;
}
var $subs = $('.sub');
$subs.each(function(indx) {
    // add drop-down arrows
    let dropdown = document.createElement("div");
    dropdown.className = "dropdown";
    dropdown.classList.add("menu-open");
    $(this).children(":first").append(dropdown);
    // position the sub_menus under the corresponding main menu items
    let main_pos = $(this).offset().left;
    let submenu_id = '#sub_' + this.id;
    $(submenu_id).css('left', main_pos + 'px');
    // mouse events
    $(this).on('mouseover', function() {
        showCloseSymbol($(this));  
        // display sub
        let id = '#sub_' + this.id;
        $(id).show();
        $(id).on('mouseover', function() {
            let main_id = '#' + this.id.substring(4);
            $(this).show();
            showCloseSymbol($(main_id));
        });
        $(id).on('mouseout', function() {
            let main_id = '#' + this.id.substring(4);
            $(this).hide();
            showOpenSymbol($(main_id));
        });
        return;
    });
    $(this).on('mouseout', function() {
        let arrow = $(this).find('.dropdown');
        arrow.removeClass('menu-close');
        arrow.addClass('menu-open');
        let id = '#sub_' + this.id;
        $(id).hide();
        return;
    });
    return;
});

/**
 * This section executes an item in the menu when it is clicked on;
 * One event def for each potential item click
 */
$('#paymts').on('click', function() {
    var def = new $.Deferred();
    var exp_form = $('#box').detach();
    modal.open({id: 'expense', width: '342px', height: '240px', 
        content: exp_form, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(exp_form);
    });
    return;
});
$('#depmo').on('click', function() {
    // make sure income hasn't already been fully deposited this month...
    let paidout = true;
    let $payments = $('table tbody tr');
    $payments.each(function() {
        let budget = $(this).find('.amt').children().last().text();
        let monthpay = $(this).children().last().text();
        if (parseInt(monthpay) < parseInt(budget)) {
            paidout = false;
            return;
        }
    });
    if (paidout) {
        let ans = confirm("It appears income has already been distributed\n" +
            "to all budgeted accounts. If you choose to proceed,\n" +
            "the funds will be placed in Undistributed Funds");
        if (!ans) {
            return;
        }
    }
    var def = new $.Deferred();
    var income_form = $('#distinc').detach();
    modal.open({id: 'income', height: '280px', width: '320px',
        content: income_form, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(income_form);
    });
    return;
});
$('#otd').on('click', function() {
    var ans = confirm("If this deposit is for regularly received " +
        "monthly income,\nplease use the 'Deposit Monthly Income' command;\n" +
        "Selecting CANCEL below will exit the current command");
    if (!ans) {
        return false;
    }
    var def = new $.Deferred();
    var funds = $('#dep').detach();
    modal.open({id: 'deposit', height: '170px', width: '220px',
        content: funds, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(funds);
    });
    return;
});
$('#movefnds').on('click', function() {
    var def = new $.Deferred();
    var xfr = $('#xfr').detach();
    modal.open({id: 'xfr', height: '226px', width: '240px',
        content: xfr, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(xfr);
    });
    return;  
});
$('#recon').on('click', function() {
    var def = new $.Deferred();
    var rec = $('#reconcile').detach();
    modal.open({id: 'recon', height: '160px', width: '240px',
        content: rec, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(rec);
    });
    return; 
});
$('#revcc').on('click', function() {
    let rev = "../utilities/reverseCharge.php";
    window.open(rev, "_self");
    return;
});
$('#revexp').on('click', function() {
    let uexp = "../utilities/undoExpense.php";
    window.open(uexp, "_self");
    return;
});
$('#addap').on('click', function() {
    var def = new $.Deferred;
    var apbox = $('#auto').detach();
    modal.open({id: 'setup_ap', height: '186px', width: '280px',
        content: apbox, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(apbox);
    }); 
    return; 
});
$('#delap').on('click', function() {
    var def = new $.Deferred();
    var dapbox = $('#delauto').detach();
    modal.open({id: 'del_ap', height: '150px', width: '240px',
        content: dapbox, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(dapbox);
    });
    return;
});
$('#vmexp').on('click', function() {
    var viewexp = "../utilities/viewCharges.php";
    window.open(viewexp, "_self");
    return;
});
$('#edcc').on('click', function() {
    var editexpense = "../edit/editCreditCharges.php";
    window.open(editexpense, "_self");
    return;
});
$('#vmrpt').on('click', function() {
    var def = new $.Deferred();
    var morpt = $('#morpt').detach();
    modal.open({id: 'morpt', width: '240px', height: '130px', 
        content: morpt, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(morpt);
    });
    return;
});
$('#varpt').on('click', function() {
    var def = new $.Deferred();
    var yrrpt = $('#yearrpt').detach();
    modal.open({id: 'yrrpt', width: '240px', height: '130px', 
        content: yrrpt, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(yrrpt);
    });
    return;
});
$('#addcd').on('click', function() {
    var def = new $.Deferred();
    var cdbox = $('#cdadd').detach();
    modal.open({id: 'addcd', width: '248px', height: '168px',
    content: cdbox, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(cdbox)
    });
    return;
});
$('#delcd').on('click', function() {
    var def = new $.Deferred();
    var crdrbox = $('#delcrdr').detach();
    modal.open({id: 'delcard', width: '240px', height: '150px',
        content: crdrbox, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(crdrbox)
    });
    return;
});
$('#acctadd').on('click', function() {
    var def = new $.Deferred();
    var adder = $('#addacct').detach();
    modal.open({id: 'addacct', width: '360px', height: '316px',
        content: adder, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(adder);
    }); 
    return;
});
$('#mvacct').on('click', function() {
    var def = new $.Deferred();
    var mvbox = $('#mv').detach();
    modal.open({id: 'mvacct', width: '260px', height: '250px',
        content: mvbox, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(mvbox);
    });
    return;
});
$('#renacct').on('click', function() {
    var def = new $.Deferred();
    var rebox = $('#rename').detach();
    modal.open({id: 'rename', width: '240px', height: '210px',
        content: rebox, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(rebox);
    });
    return;
});
$('#delacct').on('click', function() {
    var def = new $.Deferred();
    var delbox = $('#delexisting').detach();
    modal.open({id: 'delacct', width: '260px', height: '226px',
        content: delbox, deferred: def});
    $.when( def ).then(function() {
        $('#allForms').append(delbox);
    });
    return;
});
$('#edacct').on('click', function() {
    var editor = "../edit/budgetEditor.php";
    window.open(editor, "_self");
    return;
});
$('#faq').on('click', function() {
    window.open("../help/help.php?doc=FAQ.pdf", "_blank");
    return;
});
$('#siteguide').on('click', function() {
    window.open("../help/help.php?doc=Tools.pdf", "_blank");
    return;
});
$('#howto').on('click', function() {
    window.open("../help/help.php?doc=HowToBudget.pdf", "_blank");
    return;
});
$('#chgcookies').on('click', function() {
    let uchoice = $('#chglink').text();
    let newchoice;
    let newmenu;
    if (uchoice.indexOf('Reject') !== -1) {
        newchoice = 'reject';
        newmenu = 'Accept Cookies';
    } else {
        newchoice = 'accept';
        newmenu = 'Reject Cookies';
    }
    $.get("registerCookieChoice.php", {choice: newchoice}).done(function() {
        $('#chglink').text(newmenu);
        alert("You're new cookie choice has been saved");
    });
});

});