$(function() {
    // establish menu/sub-menu widths, sub-menu positions and menu icons
    var menuWidth = ['120', '120', '120', '140', 
        '120', '120', '120', '120']; // 8 main menu items
    var subWidth = ['120', '90', '130', '150', '130'];  // 4 sub-menus
    var $mainMenus = $('.menu-main');
    var navPos = $('#navbar').offset();
    var navBottom = navPos.top + $('#navbar').height() + 5 + 'px';

    // jQuery UI Menu widget - all menus w/submenus
    $(".menus").menu({
        select: function(evt, ui) {  // ui is object whose [item] is a jQuery obj
            var itemText = ui.item.text();
            var $itemDiv = ui.item.children().eq(0);
            if (!$itemDiv.hasClass('ui-state-disabled')) {
                executeItem(itemText);
            }
        }
    });

    // -------- Sub-menus --------
    var $submenus = $('.ui-menu');
    $submenus.css('background-color', '#f0fff0');
    $submenus.each(function(indx) {
        $(this).width(subWidth[indx]);
    });

    // -------- Main Menus ---------
    $mainMenus.each(function(indx) {
        var pos = $(this).offset();
        var left = pos.left;
        var menuId = '#menu-' + this.id;
        $(menuId).css('left', left);
        $(menuId).css('top', navBottom);
        $(menuId).width(menuWidth[indx]);
        $(this).on('mouseover', function() {
            $(this).find('.menu-item').find('.menuIcons').removeClass('menu-open');
            $(this).find('.menu-item').find('.menuIcons').addClass('menu-close');
            $(menuId).removeClass('menu-default');
            $(menuId).addClass('menu-active');
            $(menuId).show();
        });
        $(this).on('mouseout', function() {
            $(this).find('.menu-item').find('.menuIcons').removeClass('menu-close');
            $(this).find('.menu-item').find('.menuIcons').addClass('menu-open');
            $(menuId).removeClass('menu-default');
            $(menuId).addClass('menu-default');
            $(menuId).hide();
        });
    });

    /**
     *                ----- Menu Actions -----
     * Menus without submenus are clicked on in order to invoke (code follows);
     * Submenus utilize 'executeItem()' to invoke
     */
    var user = $('#user').text();
    
    $('#expense').on('click', function() {
        var def = new $.Deferred();
        var exp_form = $('#box').detach();
        modal.open({id: 'expense', width: '342px', height: '240px', 
            content: exp_form, deferred: def});
        $.when( def ).then(function() {
            $('#allForms').append(exp_form);
        });
    });
    
    $('#moinc').on('click', function() {
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
    });
    $('#movefnds').on('click', function() {
        var def = new $.Deferred();
        var xfr = $('#xfr').detach();
        modal.open({id: 'xfr', height: '226px', width: '240px',
            content: xfr, deferred: def});
        $.when( def ).then(function() {
            $('#allForms').append(xfr);
        });  
    });
    $('#recon').on('click', function() {
        var def = new $.Deferred();
        var rec = $('#reconcile').detach();
        modal.open({id: 'recon', height: '160px', width: '240px',
            content: rec, deferred: def});
        $.when( def ).then(function() {
            $('#allForms').append(rec);
        });  
    });
    // Submenu actions
    function executeItem(item) {
        var query_name = encodeURIComponent(user);
        switch(item) {
            case "Schedule Autopay":
                var def = new $.Deferred;
                var apbox = $('#auto').detach();
                modal.open({id: 'setup_ap', height: '196px', width: '280px',
                    content: apbox, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(apbox);
                });  
                break;
            case "Delete Autopay":
                var def = new $.Deferred();
                var dapbox = $('#delauto').detach();
                modal.open({id: 'del_ap', height: '146px', width: '240px',
                    content: dapbox, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(dapbox);
                });
                break;
            case "Add Card":
                var def = new $.Deferred();
                var cdbox = $('#cdadd').detach();
                modal.open({id: 'addcd', width: '248px', height: '160px',
                content: cdbox, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(cdbox)
                });
                break;
            case "Delete Card":
                var def = new $.Deferred();
                var crdrbox = $('#delcrdr').detach();
                modal.open({id: 'delcard', width: '240px', height: '148px',
                    content: crdrbox, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(crdrbox)
                });
                break;
            case "Edit Budget Entries":
                var editor = "../edit/budgetEditor.php?user=" + query_name;
                window.open(editor, "_self");
                break;
            case "Add Account":
                var def = new $.Deferred();
                var adder = $('#addacct').detach();
                modal.open({id: 'addacct', width: '360px', height: '320px',
                    content: adder, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(adder);
                }); 
                break;
            case "Delete Account":
                var def = new $.Deferred();
                var delbox = $('#delexisting').detach();
                modal.open({id: 'delacct', width: '260px', height: '220px',
                    content: delbox, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(delbox);
                });
                break;
            case "Move Account":
                var def = new $.Deferred();
                var mvbox = $('#mv').detach();
                modal.open({id: 'mvacct', width: '260px', height: '250px',
                    content: mvbox, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(mvbox);
                });
                break;
            case "Rename Account":
                var def = new $.Deferred();
                var rebox = $('#rename').detach();
                modal.open({id: 'rename', width: '240px', height: '206px',
                    content: rebox, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(rebox);
                });
                break;
            case "View/Manage Expenses":
                var viewexp = "../utilities/viewCharges.php?user=" + query_name;
                window.open(viewexp, "_self");
                break;
            case "Add New Charges":
                var addnew = "../edit/addCreditCharges.php?user=" + query_name;
                window.open(addnew, "_self");
                break;
            case "Edit Charges":
                var editexpense = "../edit/editCreditCharges.php?user=" + 
                    query_name;
                window.open(editexpense, "_self");
                break;
            case "Monthly Report":
                var def = new $.Deferred();
                var morpt = $('#morpt').detach();
                modal.open({id: 'morpt', width: '240px', height: '130px', 
                    content: morpt, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(morpt);
                });
                break;
             /*
            case "Annual Report":
                break;
            */
            case "Intro to Budgeting":
                window.open("../help/help.php?doc=HowToBudget.pdf", "_blank");
                break;
            case "Using Budgetizer":
                window.open("../help/help.php?doc=Tools.pdf", "_blank");
                break;
            default:
                alert(item + " is not yet implemented");
        }
    }
});
