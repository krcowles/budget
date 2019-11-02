// modal object definition
var modal = (function() {
    // Local/private to "modal"; invoked only on instantiation
    var $window = $(window);
    var $modal = $('<div class="modal" style="background-color:floralwhite;position:absolute"/>'); 
    var $content = $('<div class="modal-content"/>');
    var closeHtml = '<button class="modal-close">Cancel</button>';
    var $close = $(closeHtml);

    $modal.append($content);
    // bind to doc for repeated use of modal popups
    $(document).on('click', '.modal-close', function(ev) {
        ev.preventDefault();
        modal.close();
    });

    // functions processing forms
    function modifyData(jQloc, operand, operation, chgType) {
        // jQloc is the jQuery object holding the row cells ('td')
        cellData = parseFloat(jQloc.eq(4).children().eq(1).text());
        if (operation === 'sub') {
            cellData -= operand;
        } else { // presumably 'add'
            cellData += operand;
        }
        if (cellData < 0) {
            jQloc.eq(4).children().eq(1).addClass('negative');
        }
        cellData = $.number(cellData, 2);
        jQloc.eq(4).children().eq(1).text(cellData);
        if (chgType === 'Account' || chgType === 'Debit') {
            // fix the balance
            $balance = $('#balances').find('td').eq(4).children().eq(1);
            var balance = $balance.text().replace(",", "");
            balance = parseFloat(balance);
            if (operation === 'sub') {
                balance -= operand;
            } else {
                balance += operand;
            }
            var newbal = $.number(balance, 2);
            if (newbal < 0) {
                $balance.addClass('negative');
            }
            $balance.text(newbal);
        }  
        return;
    }
    function payExpense() {
        $content.append($close);
        $close.css('left', '220px');
        var locate = $('#expense').offset();
        $modal.css({
            top: locate.top + 40,
            left: locate.left - 100
        });
        $(window).on('resize', function() {
            $modal.css({
                top: locate.top + 40,
                left: locate.left - 100
            });
        });
        var account = $('#selacct option:selected').text();
        var $account = $('#selacct');
        var chargeto = $('#cc option:selected').text();
        var $card = $('#cc');
        var amount = 0;
        var $expensed = $('#expamt');
        $account.on('change', function() {
            account = this.value;
        });
        $card.on('change', function() {
            chargeto = this.value;
        });
        $expensed.on('change', function() {
            amount = this.value;
        });
        $('#pay').on('click', function() {
            //alert("Pay " + amount + ": Picked " + account + "; Charged " + chargeto);
            $('#roll3 tbody tr').each(function() {
                var $aname = $(this).find('td');
                var aname = $aname.eq(0).text();
                if (aname == account) {
                    modifyData($aname, amount, 'sub','Account');
                }
            });
        });
    }
    function makeDeposit() {

    }
    
    // public methods
    return {
        center: function() {
            var top = Math.max($window.height() - $modal.outerHeight(), 0) / 2;
            var left = Math.max($window.width() - $modal.outerWidth(), 0) / 2;
            $modal.css({
                    top: top + $window.scrollTop(),
                    left: left + $window.scrollLeft()
            });
        },
        open: function(settings) {
            var modid = settings.id;
            $content.empty().append(settings.content.html());
            $modal.css({
                width: settings.width || auto,
                height: settings.height || auto,
                border: '2px solid',
                padding: '8px'
            }).appendTo('body');
            // separate code for each form
            if (modid === 'expense') {
                payExpense();
            } else if (modid === 'deposit') {
                makeDeposit();
            } else {
                modal.center();
                $(window).on('resize', modal.center);
            }
        },
        close: function() {
            //$close.off('click');
            $content.empty();
            $modal.detach();
            $(window).off('resize', modal.center);
        }
    };
}());  // modal is an IIFE