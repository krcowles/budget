$(function() {
    // establish menu/sub-menu widths, sub-menu positions and menu icons
    var menuWidth = ['120', '120', '120', '140', 
        '120', '120', '120', '120']; // 8 main menu items
    var subWidth = ['90', '130', '110', '130'];  // 4 sub-menus
    var $mainMenus = $('.menu-main');
    var navPos = $('#navbar').offset();
    var navBottom = navPos.top + $('#navbar').height() + 5 + 'px';
    // page_type allows setting of icon in the menu
    var page_type = $('#page_id').text();
    var icon = '<span class="ui-icon ui-icon-circle-triangle-e"></span>';

    // jQuery UI Menu widget
    $(".menus").menu({
        select: function(evt, ui) {  // ui is object whose [item]is a jQuery obj
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
     * Menu Actions (when user chooses menu item or sub-menu item)
     */
    $('#expense').on('click', function() {
        var def = new $.Deferred();
        var exp_form = $('#box').detach();
        modal.open({id: 'expense', width: '342px', height: '220px', 
            content: exp_form, deferred: def});
        $.when( def ).then(function() {
            $('#allForms').append(exp_form);
        });
    });
    $('#moinc').on('click', function() {
        var def = new $.Deferred();
        var income_form = $('#distinc').detach();
        modal.open({id: 'income', height: '280px', width: '320px',
            content: income_form, deferred: def});
        $.when( def ).then(function() {
            $('#allForms').append(income_form);
        });
    });
    $('#otd').on('click', function() {
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
    $('#schedap').on('click', function() {
        var def = new $.Deferred;
        var apbox = $('#auto').detach();
        modal.open({id: 'setup_ap', height: '150px', width: '280px',
            content: apbox, deferred: def});
        $.when( def ).then(function() {
            $('#allForms').append(apbox);
        });  
    });
    function executeItem(item) {
        switch(item) {
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
                break;
            case "Edit Card":
                break;
            case "Edit Budget Amt":
                break;
            case "Edit Current Balance":
                break;
            case "Add Account":
                var def = new $.Deferred();
                var adder = $('#addacct').detach();
                modal.open({id: 'addacct', width: '360px', height: '360px',
                    content: adder, deferred: def});
                $.when( def ).then(function() {
                    $('#allForms').append(adder);
                }); 
                break;
            case "Delete Account":
                break;
            case "Move Account":
                break;
            case "Rename Account":
                break;
            case "Edit Expenses":
                break;
            case "Monthly Report":
                break;
            case "Annual Report":
                break;
            default:
                alert(item + " is not yet implemented");
        }
    }
});
