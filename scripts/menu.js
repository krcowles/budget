var cssmenuids=["budgettools"];    //Enter id(s) of CSS Horizontal UL menus, separated by commas
var csssubmenuoffset = -1;         //Offset of submenus from main menu. Default is 0 pixels.

$(function() {

// set main menu widths
var menuwidths = ['96', '84', '70', '60', '72',    '82', '83', '91', '98', '84','64'];
var $buds = $('.bud a');
$buds.each(function(indx) {
    $(this).width(menuwidths[indx]);
    return;
});

// handle sub-menus
const showOpenSymbol = (sub) => {
    let arrow = sub.find('.dropdown');
    arrow.removeClass('menu-close');
    arrow.addClass('menu-open');
    return;
};
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

});